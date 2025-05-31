<?php

namespace Tests\GraphQL\Installment\Mutations;

use App\Enums\FeatureEnum;
use App\Models\CentralCustomer;
use App\Models\CentralUniredCard;
use App\Models\Company\BalanceCharge;
use App\Models\Company\Customer;
use App\Models\Company\Feature;
use App\Models\Company\Installment;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\Fakers\UniredFaker;
use Tests\GraphQL\GraphQLTestCase;

class UpdateCustomerPinflCardsGraphQLMutationTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private Customer $_customer;

    protected function setUp(): void
    {
        parent::setUp();

        CentralCustomer::truncate();
        CentralUniredCard::truncate();

        BalanceCharge::query()->forceDelete();

        $feature = Feature::findByName(FeatureEnum::UNIRED_CARD_MANAGEMENT);
        $feature->update(['amount' => 5000]);

        Customer::query()->forceDelete();
        $this->_customer = Customer::factory()->create(['pinfl' => '30708091011123']);

        $installment = Installment::factory()->make(['customer_id' => $this->_customer->id, 'unired_id' => 1]);
        $installment->saveQuietly();
    }

    #[Test]
    public function successUpdateCustomerPinflCards()
    {
        UniredFaker::clientCreate();
        UniredFaker::updateHumoCards();
        UniredFaker::updateUzcardCards();
        UniredFaker::getCards();

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery(['customer_id' => $this->_customer->id]));

        $this->assertSuccessUpdateCustomerPinflCardsResponse($response);
    }

    #[Test]
    public function successUpdateCustomerPinflCardsWhenOnlyOneCardTypeFound()
    {
        UniredFaker::clientCreate();
        UniredFaker::updateHumoCards(true);
        UniredFaker::updateUzcardCards();
        UniredFaker::getCards();

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery(['customer_id' => $this->_customer->id]));

        $this->assertSuccessUpdateCustomerPinflCardsResponse($response);
    }

    #[Test]
    public function doNotChargeWhenBothCardTypesNotFound()
    {
        UniredFaker::clientCreate();
        UniredFaker::updateHumoCards(true);
        UniredFaker::updateUzcardCards(true);
        UniredFaker::getCards();

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery(['customer_id' => $this->_customer->id]));

        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure(['data' => ['updateCustomerPinflCards']]);
        $this->assertFalse($response->json('data.updateCustomerPinflCards'));

        $this->assertTrue(BalanceCharge::query()->doesntExist());
    }

    #[Test]
    public function errorValidations()
    {
        // required errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery(['customer_id' => null]));

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$customer_id" of non-null type "Int!" must not be null.'));

        // validation errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery(['customer_id' => 999999999]));

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'customer id']), $response->json('errors.0.extensions.validation.customer_id.0'));

        // validation error when trying to update cards within 24 hours of the last update
        CentralCustomer::factory()->create([
            'pinfl' => $this->_customer->pinfl,
            'unired_humo_searched_at' => Carbon::now()->subHours(23)->subMinutes(59),
            'unired_uzcard_searched_at' => Carbon::now()->subHours(23)->subMinutes(59),
        ]);

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery(['customer_id' => $this->_customer->id]));

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals('Карту клиента можно продлить только один раз в течение 24 часов', $response->json('errors.0.extensions.validation.customer_id.0'));
    }

    private function buildGraphQLQuery(array $variables = []): array
    {
        return [
            'query' => <<<'GRAPHQL'
                mutation UpdateCustomerPinflCards(
                    $customer_id: Int!
                ) {
                    updateCustomerPinflCards(customer_id: $customer_id)
                }
            GRAPHQL,
            'variables' => $variables,
        ];
    }

    private function assertSuccessUpdateCustomerPinflCardsResponse(TestResponse $response): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure(['data' => ['updateCustomerPinflCards']]);
        $this->assertTrue($response->json('data.updateCustomerPinflCards'));

        $this->_customer->refresh();

        $this->assertTrue(Carbon::now()->subSeconds(10)->lte($this->_customer->unired_cards_last_updated_at));
        $expectedData = [
            [
                'pinfl' => '413109***0086',
                'uuid' => 'KB0RWS53CAQDVHFZ9J11T560Y',
                'auto' => true,
                'is_verified' => true,
                'is_blocked' => false,
                'block_reason' => null,
                'blocked_at' => null,
                'owner' => 'John Doe',
                'pan' => '860006******6735',
                'phone' => '',
                'type' => 1,
            ],
            [
                'pinfl' => '413109***0086',
                'uuid' => '3Y1JYBWQQDY1V0T93WCS6HF1',
                'auto' => true,
                'is_verified' => true,
                'is_blocked' => true,
                'block_reason' => 'Card not found',
                'blocked_at' => '2025-01-17T08:32:44+05:00',
                'owner' => 'John Doe',
                'pan' => '986060******8244',
                'phone' => '9989939**000',
                'type' => 2,
            ],
        ];
        $uniredCards = $this->_customer->getUniredCards();
        $this->assertCount(count($expectedData), $uniredCards);
        foreach ($uniredCards as $key => $card) {
            $this->assertEquals($expectedData[$key]['pinfl'], $card->pinfl);
            $this->assertEquals($expectedData[$key]['uuid'], $card->uuid);
            $this->assertEquals($expectedData[$key]['auto'], $card->auto);
            $this->assertEquals($expectedData[$key]['is_verified'], $card->is_verified);
            $this->assertEquals($expectedData[$key]['is_blocked'], $card->is_blocked);
            $this->assertEquals($expectedData[$key]['block_reason'], $card->block_reason);
            $this->assertEquals($expectedData[$key]['blocked_at'], $card->blocked_at?->format('Y-m-d\TH:i:sP'));
            $this->assertEquals($expectedData[$key]['owner'], $card->owner);
            $this->assertEquals($expectedData[$key]['pan'], $card->pan);
            $this->assertEquals($expectedData[$key]['phone'], $card->phone);
            $this->assertEquals($expectedData[$key]['type'], $card->type?->value);
        }

        $balanceCharge = BalanceCharge::query()->first();
        $this->assertEquals(5000, $balanceCharge->amount);
        $this->assertEquals(Customer::class, $balanceCharge->chargeable_type);
    }
}
