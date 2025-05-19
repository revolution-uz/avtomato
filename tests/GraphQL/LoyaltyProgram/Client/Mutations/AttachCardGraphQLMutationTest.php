<?php

namespace Tests\GraphQL\LoyaltyProgram\Client\Mutations;

use App\Enums\Company\CardStatusEnum;
use App\Enums\FeatureEnum;
use App\Enums\SmsProviderEnum;
use App\Models\BaseFeature;
use App\Models\Company\CardConfirmationSms;
use App\Models\Company\Feature;
use App\Models\Company\LoyaltyProgram\Card;
use App\Models\Company\LoyaltyProgram\User;
use Illuminate\Support\Arr;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\Fakers\AtmosFaker;
use Tests\Fakers\SmsFaker;
use Tests\GraphQL\GraphQLTestCase;

class AttachCardGraphQLMutationTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private User $_user;

    private array $_structure = [
        'card' => [
            'id',
            'user_id',
            'number',
            'card_type',
            'owner',
            'expiry',
            'phone',
            'status',
            'balance',
            'created_at',
            'updated_at',
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
        $this->_user = $this->createLoyaltyProgramClientUser();

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

        $variables = ['number' => 9860082443467252, 'expiry' => '2808'];

        $response = $this
            ->actingAs($this->_user, 'loyalty-program-client')
            ->withHeader('signature', 'test')
            ->postJson(route('graphql.loyalty-program-client'), $this->buildGraphQLQuery($variables));

        $this->assertSuccessAttachCardResponse($response, $variables);
    }

    #[Test]
    public function cannotAttachAlreadyAttachedCard()
    {
        $card = Card::factory()->create(['user_id' => $this->_user->id, 'status' => CardStatusEnum::ACTIVE]);

        $response = $this
            ->actingAs($this->_user, 'loyalty-program-client')
            ->postJson(
                route('graphql.loyalty-program-client'),
                $this->buildGraphQLQuery(['number' => $card->number, 'expiry' => $card->expiry])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(__('api.already_attached_card'), $response->json('errors.0.extensions.validation.number.0'));
    }

    #[Test]
    public function reAttachTheDeletedCard()
    {
        AtmosFaker::getToken();
        AtmosFaker::attachCard(true);
        SmsFaker::fly();
        SmsFaker::sayqal();

        $deletedCard = Card::factory()->deleted()->create(['status' => CardStatusEnum::ACTIVE, 'user_id' => $this->_user->id]);
        $variables = ['number' => $deletedCard->number, 'expiry' => $deletedCard->expiry];

        $response = $this
            ->actingAs($this->_user, 'loyalty-program-client')
            ->postJson(route('graphql.loyalty-program-client'), $this->buildGraphQLQuery($variables));

        $this->assertSuccessAttachCardResponse($response, $variables);

        $deletedCard->refresh();
        $this->assertNotEquals($deletedCard->id, $response->json('data.attachCard.card.id'));

        $this->assertEquals($response->json('data.attachCard.card.id'), $response->json('data.attachCard.attachData.transaction_id'));
        $this->assertEquals($deletedCard->phone, $response->json('data.attachCard.attachData.phone'));
        $this->assertEquals('OK', $response->json('data.attachCard.attachData.result_code'));
        $this->assertNull($response->json('data.attachCard.attachData.result_description'));
    }

    #[Test]
    public function cannotAttachAlreadyAttachedCardToAnotherUser()
    {
        $card = Card::factory()->create(['status' => CardStatusEnum::ACTIVE]);
        $variables = ['number' => $card->number, 'expiry' => $card->expiry];

        $response = $this
            ->actingAs($this->_user, 'loyalty-program-client')
            ->postJson(route('graphql.loyalty-program-client'), $this->buildGraphQLQuery($variables));

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('api.card_in_use_by_another_user'), $response->json('errors.0.extensions.validation.number.0'));
    }

    #[Test]
    public function successAttachAlreadyAttachedCardToAtmos()
    {
        AtmosFaker::getToken();
        AtmosFaker::attachCard(partner_has_card: true);
        SmsFaker::fly();
        SmsFaker::sayqal();

        $variables = ['number' => 9860082443467252, 'expiry' => '2808'];

        $response = $this
            ->actingAs($this->_user, 'loyalty-program-client')
            ->postJson(route('graphql.loyalty-program-client'), $this->buildGraphQLQuery($variables));

        $this->assertSuccessAttachCardResponse($response, $variables);

        $this->assertEquals($response->json('data.attachCard.card.id'), $response->json('data.attachCard.attachData.transaction_id'));
        $this->assertEquals('OK', $response->json('data.attachCard.attachData.result_code'));
        $this->assertNull($response->json('data.attachCard.attachData.result_description'));
    }

    #[Test]
    public function successAttemptToAttachSecondTime()
    {
        SmsFaker::fly();
        SmsFaker::sayqal();

        $card = Card::factory()->create(['status' => CardStatusEnum::INACTIVE, 'user_id' => $this->_user->id]);
        $variables = ['number' => $card->number, 'expiry' => $card->expiry];

        $response = $this
            ->actingAs($this->_user, 'loyalty-program-client')
            ->postJson(route('graphql.loyalty-program-client'), $this->buildGraphQLQuery($variables));

        $this->assertSuccessAttachCardResponse($response, $variables);
        $sms = CardConfirmationSms::query()->latest()->firstOrFail();
        $this->assertEquals($card->phone, $sms->phone);

        // Check for not sending second confirmation code
        $response = $this
            ->actingAs($this->_user, 'loyalty-program-client')
            ->postJson(route('graphql.loyalty-program-client'), $this->buildGraphQLQuery($variables));

        $this->assertSuccessAttachCardResponse($response, $variables);
        $this->assertEquals(1, CardConfirmationSms::query()->count());
    }

    #[Test]
    public function errorValidation()
    {
        // GraphQL type error
        $response = $this
            ->actingAs($this->_user, 'loyalty-program-client')
            ->postJson(
                route('graphql.loyalty-program-client'),
                $this->buildGraphQLQuery(['number' => null, 'expiry' => null])
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$number" of non-null type "BigInteger!" must not be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$expiry" of non-null type "String!" must not be null.'));

        // Rule error
        $response = $this
            ->actingAs($this->_user, 'loyalty-program-client')
            ->postJson(
                route('graphql.loyalty-program-client'),
                $this->buildGraphQLQuery(['number' => 12345678901234561, 'expiry' => '12345'])
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.digits', ['attribute' => 'number', 'digits' => 16]), $response->json('errors.0.extensions.validation.number.0'));
        $this->assertEquals(trans('validation.digits', ['attribute' => 'expiry', 'digits' => 4]), $response->json('errors.0.extensions.validation.expiry.0'));
    }

    public function buildGraphQLQuery(array $variables = []): array
    {
        return [
            'query' => <<<GRAPHQL
                mutation AttachCard(\$number: BigInteger!, \$expiry: String!) {
                    attachCard(number: \$number, expiry: \$expiry) {
                        {$this->convertIntoQuery($this->_structure)}
                    }
                }
            GRAPHQL,
            'variables' => $variables,
        ];
    }

    private function assertSuccessAttachCardResponse(TestResponse $response, array $variables): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure(['data' => ['attachCard' => $this->_structure]]);

        $card = $this->_user->cards()->first();
        $this->assertNotNull($card);

        $this->assertEquals($card->number, $variables['number']);
        $this->assertEquals($card->expiry, $variables['expiry']);

        // Card
        $this->assertEquals($card->id, $response->json('data.attachCard.card.id'));
        $this->assertEquals($card->user_id, $response->json('data.attachCard.card.user_id'));
        $this->assertEquals($card->pan, $response->json('data.attachCard.card.number'));
        $this->assertEquals($card->card_type, $response->json('data.attachCard.card.card_type'));
        $this->assertEquals($card->owner, $response->json('data.attachCard.card.owner'));
        $this->assertEquals($card->expiry, $response->json('data.attachCard.card.expiry'));
        $this->assertEquals($card->phone, $response->json('data.attachCard.card.phone'));
        $this->assertEquals($card->status->value, $response->json('data.attachCard.card.status'));
        $this->assertEquals($card->balance, $response->json('data.attachCard.card.balance'));
        $this->assertEquals($card->created_at->timestamp, $response->json('data.attachCard.card.created_at'));
        $this->assertEquals($card->updated_at->timestamp, $response->json('data.attachCard.card.updated_at'));

        // Attach Data
        $this->assertArrayHasKey('transaction_id', $response->json('data.attachCard.attachData'));
        $this->assertArrayHasKey('phone', $response->json('data.attachCard.attachData'));
        $this->assertArrayHasKey('result_code', $response->json('data.attachCard.attachData'));
        $this->assertArrayHasKey('result_description', $response->json('data.attachCard.attachData'));
    }
}
