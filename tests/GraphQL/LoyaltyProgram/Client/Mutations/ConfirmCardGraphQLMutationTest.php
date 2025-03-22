<?php

namespace Tests\GraphQL\LoyaltyProgram\Client\Mutations;

use App\Enums\Company\CardStatusEnum;
use App\Enums\Company\CompanySettingsEnum;
use App\Enums\FeatureEnum;
use App\Models\Company\CardConfirmationSms;
use App\Models\Company\CompanySettings;
use App\Models\Company\Feature;
use App\Models\Company\LoyaltyProgram\Card;
use App\Models\Company\LoyaltyProgram\User;
use Illuminate\Support\Arr;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\Fakers\AtmosFaker;
use Tests\GraphQL\GraphQLTestCase;

class ConfirmCardGraphQLMutationTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private User $_user;

    private Card $_card;

    private array $_structure = [
        'id',
        'user_id',
        'number',
        'card_type',
        'is_connected',
        'owner',
        'expiry',
        'phone',
        'status',
        'balance',
        'created_at',
        'updated_at',
        'user' => [
            'id',
            'phone',
            'first_name',
            'last_name',
            'birth_date',
            'created_at',
            'updated_at',
            'deleted_at',
        ],
    ];

    protected function setUp(): void
    {
        parent::setUp();

        AtmosFaker::getToken();

        Card::query()->forceDelete();
        $this->_user = $this->createLoyaltyProgramClientUser();
        $this->_card = Card::factory()->create(['user_id' => $this->_user->id, 'status' => CardStatusEnum::INACTIVE]);

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
        AtmosFaker::confirmCard();
        $this->inactivateLoyaltyProgramBalanceCheck();

        $variables = [
            'card_id' => $this->_card->id,
            'transaction_id' => 999999,
            'otp' => '1',
        ];

        $response = $this
            ->actingAs($this->_user, 'loyalty-program-client')
            ->postJson(route('graphql.loyalty-program-client'), $this->buildGraphQLQuery($variables));

        $this->assertSuccessConfirmCardResponse($response);
    }

    #[Test]
    public function emptyResponseFromAtmos(): void
    {
        AtmosFaker::emptyResponse();

        $variables = [
            'card_id' => $this->_card->id,
            'transaction_id' => 999999,
            'otp' => '1',
        ];

        $response = $this
            ->actingAs($this->_user, 'loyalty-program-client')
            ->postJson(route('graphql.loyalty-program-client'), $this->buildGraphQLQuery($variables));

        $this->assertEquals('Internal server error', $response->json('errors.0.message'));
        $this->assertEquals('Unexpected response', $response->json('errors.0.extensions.debugMessage'));
    }

    #[Test]
    public function nullResponseFromAtmos(): void
    {
        AtmosFaker::nullResponse();

        $variables = [
            'card_id' => $this->_card->id,
            'transaction_id' => 999999,
            'otp' => '1',
        ];

        $response = $this
            ->actingAs($this->_user, 'loyalty-program-client')
            ->postJson(route('graphql.loyalty-program-client'), $this->buildGraphQLQuery($variables));

        $this->assertEquals('Internal server error', $response->json('errors.0.message'));
        $this->assertEquals('Unexpected response', $response->json('errors.0.extensions.debugMessage'));
    }

    #[Test]
    public function successConfirmAlreadyAttachedCard()
    {
        $this->inactivateLoyaltyProgramBalanceCheck();

        $authAttempt = CardConfirmationSms::factory()->create(['phone' => $this->_card->phone]);

        $variables = [
            'card_id' => $this->_card->id,
            'transaction_id' => $this->_card->id,
            'otp' => $authAttempt->code,
        ];

        $response = $this
            ->actingAs($this->_user, 'loyalty-program-client')
            ->postJson(route('graphql.loyalty-program-client'), $this->buildGraphQLQuery($variables));

        $this->assertSuccessConfirmCardResponse($response);
    }

    #[Test]
    public function errorValidation()
    {
        // GraphQL type error
        $response = $this
            ->actingAs($this->_user, 'loyalty-program-client')
            ->postJson(
                route('graphql.loyalty-program-client'),
                $this->buildGraphQLQuery(['card_id' => null, 'transaction_id' => null, 'otp' => null])
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$card_id" of non-null type "Int!" must not be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$transaction_id" of non-null type "BigInteger!" must not be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$otp" of non-null type "String!" must not be null.'));

        // Rule error
        $response = $this
            ->actingAs($this->_user, 'loyalty-program-client')
            ->postJson(
                route('graphql.loyalty-program-client'),
                $this->buildGraphQLQuery(['card_id' => 999999999, 'transaction_id' => 1, 'otp' => '1'])
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'card id']), $response->json('errors.0.extensions.validation.card_id.0'));
    }

    #[Test]
    public function failConfirmAlreadyAttachedCard()
    {
        $this->inactivateLoyaltyProgramBalanceCheck();

        // Wrong otp
        $authAttempt = CardConfirmationSms::factory()->create(['phone' => $this->_card->phone]);
        $variables = [
            'card_id' => $this->_card->id,
            'transaction_id' => $this->_card->id,
            'otp' => $authAttempt->code . '1',
        ];
        $response = $this
            ->actingAs($this->_user, 'loyalty-program-client')
            ->postJson(route('graphql.loyalty-program-client'), $this->buildGraphQLQuery($variables));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(__('api.otp_was_wrong'), $response->json('errors.0.extensions.validation.otp.0'));
        $this->_card->refresh();
        $this->assertEquals(CardStatusEnum::INACTIVE, $this->_card->status);

        // Expired otp
        $authAttempt = CardConfirmationSms::factory()->expired()->create(['phone' => $this->_card->phone]);
        $variables = [
            'card_id' => $this->_card->id,
            'transaction_id' => $this->_card->id,
            'otp' => $authAttempt->code,
        ];
        $response = $this
            ->actingAs($this->_user, 'loyalty-program-client')
            ->postJson(route('graphql.loyalty-program-client'), $this->buildGraphQLQuery($variables));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(__('api.otp_was_wrong'), $response->json('errors.0.extensions.validation.otp.0'));
        $this->_card->refresh();
        $this->assertEquals(CardStatusEnum::INACTIVE, $this->_card->status);
    }

    #[Test]
    public function successConfirmCardWithBalanceCheck()
    {
        AtmosFaker::confirmCard();
        AtmosFaker::getCardInfo();

        $variables = [
            'card_id' => $this->_card->id,
            'transaction_id' => 999999,
            'otp' => '1',
        ];

        $response = $this
            ->actingAs($this->_user, 'loyalty-program-client')
            ->postJson(route('graphql.loyalty-program-client'), $this->buildGraphQLQuery($variables));

        $this->assertSuccessConfirmCardResponse($response);
    }

    #[Test]
    public function failConfirmCardWithInsufficientBalance()
    {
        AtmosFaker::confirmCard();
        AtmosFaker::getCardInfo(199999);

        $variables = [
            'card_id' => $this->_card->id,
            'transaction_id' => 999999,
            'otp' => '1',
        ];

        $response = $this
            ->actingAs($this->_user, 'loyalty-program-client')
            ->postJson(route('graphql.loyalty-program-client'), $this->buildGraphQLQuery($variables));

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(__('api.card_balance_insufficient', ['balance' => 200000]), $response->json('errors.0.extensions.validation.card_id.0'));
        $this->_card->refresh();
        $this->assertEquals(CardStatusEnum::INACTIVE, $this->_card->status);
    }

    #[Test]
    public function errorConfirmCardWithAtmosBadResponse()
    {
        AtmosFaker::confirmCard();
        AtmosFaker::getCardInfo(isFailed: true);

        $variables = [
            'card_id' => $this->_card->id,
            'transaction_id' => 999999,
            'otp' => '1',
        ];

        $response = $this
            ->actingAs($this->_user, 'loyalty-program-client')
            ->postJson(route('graphql.loyalty-program-client'), $this->buildGraphQLQuery($variables));

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('Unable to retrieve card balance', $response->json('errors.0.message'));
        $this->_card->refresh();
        $this->assertEquals(CardStatusEnum::INACTIVE, $this->_card->status);
    }

    public function buildGraphQLQuery(array $variables = []): array
    {
        return [
            'query' => <<<GRAPHQL
                mutation ConfirmCard(\$card_id: Int!, \$transaction_id: BigInteger!, \$otp: String!) {
                    confirmCard(card_id: \$card_id, transaction_id: \$transaction_id, otp: \$otp) {
                        {$this->convertIntoQuery($this->_structure)}
                    }
                }
            GRAPHQL,
            'variables' => $variables,
        ];
    }

    private function assertSuccessConfirmCardResponse(TestResponse $response): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure(['data' => ['confirmCard' => $this->_structure]]);

        $this->_card->refresh();

        $this->assertEquals($this->_card->id, $response->json('data.confirmCard.id'));
        $this->assertEquals($this->_card->user_id, $response->json('data.confirmCard.user_id'));
        $this->assertEquals($this->_card->pan, $response->json('data.confirmCard.number'));
        $this->assertEquals($this->_card->card_type, $response->json('data.confirmCard.card_type'));
        $this->assertEquals($this->_card->owner, $response->json('data.confirmCard.owner'));
        $this->assertEquals($this->_card->expiry, $response->json('data.confirmCard.expiry'));
        $this->assertTrue($response->json('data.confirmCard.is_connected'));
        $this->assertEquals($this->_card->phone, $response->json('data.confirmCard.phone'));
        $this->assertEquals(CardStatusEnum::ACTIVE->value, $response->json('data.confirmCard.status'));
        $this->assertEquals($this->_card->balance, $response->json('data.confirmCard.balance'));
        $this->assertEquals($this->_card->created_at->timestamp, $response->json('data.confirmCard.created_at'));
        $this->assertEquals($this->_card->updated_at->timestamp, $response->json('data.confirmCard.updated_at'));
    }

    private function inactivateLoyaltyProgramBalanceCheck()
    {
        CompanySettings::query()
            ->where('name', CompanySettingsEnum::LOYALTY_PROGRAM_BALANCE_CHECK)
            ->update(['value' => false]);
    }
}
