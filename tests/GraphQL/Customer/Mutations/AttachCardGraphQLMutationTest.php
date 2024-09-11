<?php

namespace Tests\GraphQL\Customer\Mutations;

use App\Enums\Company\CardStatusEnum;
use App\Enums\FeatureEnum;
use App\Enums\SmsProviderEnum;
use App\Models\BaseFeature;
use App\Models\Company\Card;
use App\Models\Company\CardConfirmationSms;
use App\Models\Company\Customer;
use App\Models\Company\Feature;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\Fakers\AtmosFaker;
use Tests\Fakers\SmsFaker;
use Tests\GraphQL\GraphQLTestCase;

class AttachCardGraphQLMutationTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private Customer $_customer;

    private array $_input;

    private array $_structure = [
        'card' => [
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
        ],
        'attachData' => [
            'transaction_id',
            'phone',
            'result_code',
            'result_description',
        ],
    ];

    protected function setUp(): void
    {
        parent::setUp();

        Card::query()->forceDelete();
        $this->_customer = Customer::factory()->create();

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

        $baseFeature = BaseFeature::where('name', FeatureEnum::SMS)->firstOrFail();
        $baseFeature->original_data = [
            'provider' => SmsProviderEnum::randomValue(),
            'username' => 'username',
            'secret' => 'secret',
        ];
        $baseFeature->saveOrFail();
    }

    #[Test]
    public function successAttachCard()
    {
        AtmosFaker::getToken();
        AtmosFaker::attachCard();

        $this->_input = [
            'customer_id' => $this->_customer->id,
            'number' => 9860082443467252,
            'expiry' => '2808',
        ];

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.customer'),
                $this->buildGraphQLQuery($this->_input)
            );

        $this->assertSuccessAttachCardResponse($response);
        $this->assertFalse($response->json('data.attachCard.card.is_connected'));
    }

    #[Test]
    public function cannotAttachAlreadyAttachedCardToTheCustomer()
    {
        $card = Card::factory()->create(['status' => CardStatusEnum::ACTIVE, 'customer_id' => $this->_customer->id]);

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.customer'),
                $this->buildGraphQLQuery(['customer_id' => $this->_customer->id, 'number' => $card->number, 'expiry' => $card->expiry])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals('Карта уже привязана к этому клиенту', $response->json('errors.0.extensions.validation.number.0'));
    }

    #[Test]
    public function reAttachTheDeletedCardToTheCustomer()
    {
        AtmosFaker::getToken();
        AtmosFaker::attachCard(true);
        SmsFaker::fly();
        SmsFaker::sayqal();
        $deletedCard = Card::factory()->create(['status' => CardStatusEnum::ACTIVE, 'customer_id' => $this->_customer->id, 'deleted_at' => Carbon::now()]);
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.customer'),
                $this->buildGraphQLQuery(['customer_id' => $this->_customer->id, 'number' => $deletedCard->number, 'expiry' => $deletedCard->expiry])
            );
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'attachCard' => $this->_structure,
            ],
        ]);

        $card = Card::query()->findOrFail($response->json('data.attachCard.card.id'));
        $this->assertEquals($card->id, $response->json('data.attachCard.card.id'));
        $this->assertEquals($card->customer_id, $response->json('data.attachCard.card.customer_id'));
        $this->assertEquals($card->pan, $response->json('data.attachCard.card.number'));
        $this->assertEquals($card->card_type, $response->json('data.attachCard.card.card_type'));
        $this->assertEquals($card->owner, $response->json('data.attachCard.card.owner'));
        $this->assertEquals($card->expiry, $response->json('data.attachCard.card.expiry'));
        $this->assertEquals($card->is_connected, $response->json('data.attachCard.card.is_connected'));
        $this->assertEquals($card->phone, $response->json('data.attachCard.card.phone'));
        $this->assertEquals(CardStatusEnum::INACTIVE->value, $response->json('data.attachCard.card.status'));
        $this->assertEquals((float) $card->balance, $response->json('data.attachCard.card.balance'));
        $this->assertEquals($card->created_at->timestamp, $response->json('data.attachCard.card.created_at'));
        $this->assertEquals($card->updated_at->timestamp, $response->json('data.attachCard.card.updated_at'));
        $this->assertEquals($card->customer_id, $this->_customer->id);
        $this->assertEquals($card->number, $deletedCard->number);
        $this->assertEquals($card->expiry, $deletedCard->expiry);
        $this->assertIsArray($response->json('data.attachCard.attachData'));
        $this->assertEquals($response->json('data.attachCard.card.id'), $response->json('data.attachCard.attachData.transaction_id'));
        $this->assertEquals($card->phone, $response->json('data.attachCard.attachData.phone'));
        $this->assertEquals('OK', $response->json('data.attachCard.attachData.result_code'));
        $this->assertNull($response->json('data.attachCard.attachData.result_description'));
    }

    #[Test]
    public function successAttachAlreadyAttachedCardToAnotherCustomer()
    {
        SmsFaker::fly();
        SmsFaker::sayqal();

        $card = Card::factory()->create(['status' => CardStatusEnum::ACTIVE]);

        $this->_input = [
            'customer_id' => $this->_customer->id,
            'number' => $card->number,
            'expiry' => $card->expiry,
        ];

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.customer'),
                $this->buildGraphQLQuery($this->_input)
            );

        $this->assertSuccessAttachCardResponse($response);

        $this->assertEquals($response->json('data.attachCard.card.id'), $response->json('data.attachCard.attachData.transaction_id'));
        $this->assertEquals($card->phone, $response->json('data.attachCard.attachData.phone'));
        $this->assertEquals('OK', $response->json('data.attachCard.attachData.result_code'));
        $this->assertNull($response->json('data.attachCard.attachData.result_description'));
    }

    #[Test]
    public function successAttachAlreadyAttachedCardToAtmos()
    {
        AtmosFaker::getToken();
        AtmosFaker::attachCard(partner_has_card: true);
        SmsFaker::fly();
        SmsFaker::sayqal();

        $this->_input = [
            'customer_id' => $this->_customer->id,
            'number' => 9860082443467252,
            'expiry' => '2808',
        ];

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.customer'),
                $this->buildGraphQLQuery($this->_input)
            );

        $this->assertSuccessAttachCardResponse($response);

        $this->assertEquals($response->json('data.attachCard.card.id'), $response->json('data.attachCard.attachData.transaction_id'));
        $this->assertEquals('OK', $response->json('data.attachCard.attachData.result_code'));
        $this->assertNull($response->json('data.attachCard.attachData.result_description'));
    }

    #[Test]
    public function successAttemptToAttachSecondTime()
    {
        SmsFaker::fly();
        SmsFaker::sayqal();
        $card = Card::factory()->create(['status' => CardStatusEnum::INACTIVE, 'customer_id' => $this->_customer->id]);

        $this->_input = [
            'customer_id' => $this->_customer->id,
            'number' => $card->number,
            'expiry' => $card->expiry,
        ];

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.customer'),
                $this->buildGraphQLQuery($this->_input)
            );
        $this->assertSuccessAttachCardResponse($response);
        $sms = CardConfirmationSms::query()->latest()->firstOrFail();
        $this->assertEquals($card->phone, $sms->phone);

        // Check for not sending second confirmation code
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.customer'),
                $this->buildGraphQLQuery([
                    'customer_id' => $this->_customer->id,
                    'number' => $card->number,
                    'expiry' => $card->expiry,
                ])
            );
        $this->assertSuccessAttachCardResponse($response);
        $this->assertEquals(1, CardConfirmationSms::query()->count());
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
                    'number' => null,
                    'expiry' => null,
                ])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$customer_id" of non-null type "Int!" must not be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$number" of non-null type "BigInteger!" must not be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$expiry" of non-null type "String!" must not be null.'));

        // validation errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.customer'),
                $this->buildGraphQLQuery([
                    'customer_id' => 999999999,
                    'number' => 12345678901234561,
                    'expiry' => '12345',
                ])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'customer id']), $response->json('errors.0.extensions.validation.customer_id.0'));
        $this->assertEquals(trans('validation.digits', ['attribute' => 'number', 'digits' => 16]), $response->json('errors.0.extensions.validation.number.0'));
        $this->assertEquals(trans('validation.digits', ['attribute' => 'expiry', 'digits' => 4]), $response->json('errors.0.extensions.validation.expiry.0'));
    }

    private function buildGraphQLQuery(array $variables = []): array
    {
        $query = [];
        $query['variables'] = $variables;
        $query['query'] = '
            mutation AttachCard($customer_id: Int!, $number: BigInteger!, $expiry: String!) {
                attachCard(customer_id: $customer_id, number: $number, expiry: $expiry) {
                    ' . $this->convertIntoQuery($this->_structure) . '
                }
            }
        ';

        return $query;
    }

    private function assertSuccessAttachCardResponse(TestResponse $response): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'attachCard' => $this->_structure,
            ],
        ]);

        $card = Card::query()->findOrFail($response->json('data.attachCard.card.id'));
        $this->assertEquals($card->id, $response->json('data.attachCard.card.id'));
        $this->assertEquals($card->customer_id, $response->json('data.attachCard.card.customer_id'));
        $this->assertEquals($card->pan, $response->json('data.attachCard.card.number'));
        $this->assertEquals($card->card_type, $response->json('data.attachCard.card.card_type'));
        $this->assertEquals($card->owner, $response->json('data.attachCard.card.owner'));
        $this->assertEquals($card->expiry, $response->json('data.attachCard.card.expiry'));
        $this->assertEquals($card->is_connected, $response->json('data.attachCard.card.is_connected'));
        $this->assertEquals($card->phone, $response->json('data.attachCard.card.phone'));
        $this->assertEquals(CardStatusEnum::INACTIVE->value, $response->json('data.attachCard.card.status'));
        $this->assertEquals((float) $card->balance, $response->json('data.attachCard.card.balance'));
        $this->assertEquals($card->created_at->timestamp, $response->json('data.attachCard.card.created_at'));
        $this->assertEquals($card->updated_at->timestamp, $response->json('data.attachCard.card.updated_at'));

        $this->assertEquals($card->customer_id, $this->_input['customer_id']);
        $this->assertEquals($card->number, $this->_input['number']);
        $this->assertEquals($card->expiry, $this->_input['expiry']);

        $this->assertIsArray($response->json('data.attachCard.attachData'));
    }
}
