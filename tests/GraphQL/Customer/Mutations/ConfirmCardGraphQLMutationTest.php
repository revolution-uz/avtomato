<?php

namespace Tests\GraphQL\Customer\Mutations;

use App\Enums\Company\CardStatusEnum;
use App\Enums\FeatureEnum;
use App\Models\Company\Card;
use App\Models\Company\CardConfirmationSms;
use App\Models\Company\Customer;
use App\Models\Company\Feature;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\Fakers\AtmosFaker;
use Tests\GraphQL\GraphQLTestCase;

class ConfirmCardGraphQLMutationTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private Customer $_customer;

    private Card $_card;

    private array $_structure = [
        'id',
        'customer_id',
        'number',
        'card_type',
        'owner',
        'expiry',
        'is_connected',
        'phone',
        'status',
        'balance',
        'created_at',
        'updated_at',
        'customer' => [
            'id',
            'pinfl',
            'tin',
            'passport_series',
            'passport_number',
            'passport_given_date',
            'passport_expiration_date',
            'passport_given_place',
            'passport_citizenship',
            'first_name',
            'last_name',
            'middle_name',
            'birth_date',
            'birth_place',
            'gender',
            'created_at',
            'updated_at',
            'deleted_at',
        ],
    ];

    protected function setUp(): void
    {
        parent::setUp();

        AtmosFaker::getToken();
        AtmosFaker::confirmCard();

        Customer::query()->forceDelete();
        $this->_customer = Customer::factory()->create();
        Card::query()->forceDelete();
        $this->_card = Card::factory()->create(['customer_id' => $this->_customer->id]);
        $feature = Feature::findByName(FeatureEnum::ATMOS_CARD_PAY);
        $feature->data = [
            'username' => 'username',
            'password' => 'password',
            'store_id' => '1',
            'expires_in' => 3600,
            'access_token' => 'access_token',
            'token_type' => 'token_type',
        ];
        $feature->saveOrFail();
    }

    #[Test]
    public function successConfirmCard()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.customer'),
                $this->buildGraphQLQuery([
                    'customer_id' => $this->_customer->id,
                    'card_id' => $this->_card->id,
                    'transaction_id' => 999999,
                    'otp' => '1',
                ])
            );

        $this->assertSuccessConfirmCardResponse($response);
    }

    #[Test]
    public function successConfirmAlreadyAttachedCard()
    {
        $authAttempt = CardConfirmationSms::query()->create(['phone' => $this->_card->phone, 'code' => rand(100000, 999999)]);

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.customer'),
                $this->buildGraphQLQuery([
                    'customer_id' => $this->_customer->id,
                    'card_id' => $this->_card->id,
                    'transaction_id' => $this->_card->id,
                    'otp' => $authAttempt->code,
                ])
            );

        $this->assertSuccessConfirmCardResponse($response);
    }

    #[Test]
    public function errorValidation()
    {
        // required errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.customer'),
                $this->buildGraphQLQuery([
                    'customer_id' => null,
                    'card_id' => null,
                    'transaction_id' => null,
                    'otp' => null,
                ])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$customer_id" of non-null type "Int!" must not be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$card_id" of non-null type "Int!" must not be null.'));

        // validation errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.customer'),
                $this->buildGraphQLQuery([
                    'customer_id' => 999999999,
                    'card_id' => 999999999,
                    'transaction_id' => 1,
                    'otp' => '1',
                ])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'customer id']), $response->json('errors.0.extensions.validation.customer_id.0'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'card id']), $response->json('errors.0.extensions.validation.card_id.0'));

        // validation errors
        $inp = [
            'customer_id' => 999999999,
            'card_id' => 999999999,
            'transaction_id' => 'transaction_id',
            'otp' => 1,
        ];

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.customer'),
                $this->buildGraphQLQuery($inp)
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === "Variable \"\$transaction_id\" got invalid value \"{$inp['transaction_id']}\"; {$inp['transaction_id']} cannot represent a big integer."));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === "Variable \"\$otp\" got invalid value {$inp['otp']}; String cannot represent a non string value: {$inp['otp']}"));
    }

    #[Test]
    public function failConfirmAlreadyAttachedCard()
    {
        // wrong otp
        $authAttempt = CardConfirmationSms::query()->create(['phone' => $this->_card->phone, 'code' => '111111']);
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.customer'),
                $this->buildGraphQLQuery([
                    'customer_id' => $this->_customer->id,
                    'card_id' => $this->_card->id,
                    'transaction_id' => $this->_card->id,
                    'otp' => '222222',
                ])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(__('api.otp_was_wrong'), $response->json('errors.0.extensions.validation.otp.0'));

        // expired otp
        $authAttempt->created_at = Carbon::now()->subMinutes(3);
        $authAttempt->saveOrFail();

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.customer'),
                $this->buildGraphQLQuery([
                    'customer_id' => $this->_customer->id,
                    'card_id' => $this->_card->id,
                    'transaction_id' => $this->_card->id,
                    'otp' => $authAttempt->code,
                ])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(__('api.otp_was_wrong'), $response->json('errors.0.extensions.validation.otp.0'));
    }

    private function buildGraphQLQuery(array $variables = []): array
    {
        $query = [];
        $query['variables'] = $variables;
        $query['query'] = '
            mutation ConfirmCard(
                $customer_id: Int!
                $card_id: Int!
                $transaction_id: BigInteger
                $otp: String
            ) {
                confirmCard(
                    customer_id: $customer_id
                    card_id: $card_id
                    transaction_id: $transaction_id
                    otp: $otp
                ) {
                    ' . $this->convertIntoQuery($this->_structure) . '
                }
            }
        ';

        return $query;
    }

    private function assertSuccessConfirmCardResponse(TestResponse $response): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'confirmCard' => $this->_structure,
            ],
        ]);

        $card = Card::query()->find($response->json('data.confirmCard.id'));
        $this->assertNotNull($card);
        $this->assertEquals($card->id, $response->json('data.confirmCard.id'));
        $this->assertEquals($card->customer_id, $response->json('data.confirmCard.customer_id'));
        $this->assertEquals($card->pan, $response->json('data.confirmCard.number'));
        $this->assertEquals($card->card_type, $response->json('data.confirmCard.card_type'));
        $this->assertEquals($card->owner, $response->json('data.confirmCard.owner'));
        $this->assertEquals($card->expiry, $response->json('data.confirmCard.expiry'));
        $this->assertTrue($response->json('data.confirmCard.is_connected'));
        $this->assertEquals($card->phone, $response->json('data.confirmCard.phone'));
        $this->assertEquals(CardStatusEnum::ACTIVE->value, $response->json('data.confirmCard.status'));
        $this->assertEquals((float) $card->balance, $response->json('data.confirmCard.balance'));
        $this->assertEquals($card->created_at->timestamp, $response->json('data.confirmCard.created_at'));
        $this->assertEquals($card->updated_at->timestamp, $response->json('data.confirmCard.updated_at'));
    }
}
