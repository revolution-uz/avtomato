<?php

namespace GraphQL\LoyaltyProgram\Client\Mutations;

use App\Enums\Company\CardStatusEnum;
use App\Models\Company\LoyaltyProgram\Card;
use App\Models\Company\LoyaltyProgram\Subscription;
use App\Models\Company\LoyaltyProgram\Tariff;
use App\Models\Company\LoyaltyProgram\User;
use Illuminate\Support\Arr;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class CreateSubscriptionGraphQLMutationTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private User $_activeUser;
    private Tariff $_activeTariff;

    private array $_structure = [
        'id',
        'is_active',
        'remaining_limit_amount',
        'tariff_id',
        'user_id',
        'expired_at',
        'created_at',
        'updated_at',
        'tariff' => [
            'id',
            'name_uz',
            'name_ru',
            'amount',
            'free_months',
            'subscription_months',
            'is_free_for_previous_subscribers',
            'limit_amount',
            'is_active',
            'created_at',
            'updated_at',
            'deleted_at',
        ],
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

        $this->_activeUser = $this->createLoyaltyProgramClientUser();
        Card::factory()->create(['user_id' => $this->_activeUser->id, 'status' => CardStatusEnum::ACTIVE]);
        $this->_activeTariff = Tariff::factory()->create(['is_active' => true]);
        Subscription::query()->forceDelete();
    }

    #[Test]
    public function successCreateSubscription()
    {
        $variables = ['tariff_id' => $this->_activeTariff->id];

        $response = $this
            ->actingAs($this->_activeUser, 'loyalty-program-client')
            ->postJson(route('graphql.loyalty-program-client'), $this->buildGraphQLQuery($variables));

        $this->assertSuccessCreateSubscriptionResponse($response, $variables);
    }

    #[Test]
    public function errorValidation(): void
    {
        // GraphQL type errors
        $response = $this
            ->actingAs($this->_activeUser, 'loyalty-program-client')
            ->postJson(route('graphql.loyalty-program-client'), $this->buildGraphQLQuery());
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$tariff_id" of required type "Int!" was not provided.'));

        // Rule errors
        $response = $this
            ->actingAs($this->_activeUser, 'loyalty-program-client')
            ->postJson(route('graphql.loyalty-program-client'), $this->buildGraphQLQuery(['tariff_id' => 999999999]));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'tariff id']), $response->json('errors.0.extensions.validation.tariff_id.0'));

        // User with an incomplete profile cannot create a subscription
        $user = User::factory()->create(['first_name' => null]);
        $response = $this
            ->actingAs($user, 'loyalty-program-client')
            ->postJson(route('graphql.loyalty-program-client'), $this->buildGraphQLQuery(['tariff_id' => $this->_activeTariff->id]));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals('Please complete your profile information first.', $response->json('errors.0.extensions.validation.user.0'));

        // User without an active card cannot create a subscription
        $user = User::factory()->create();
        $response = $this
            ->actingAs($user, 'loyalty-program-client')
            ->postJson(route('graphql.loyalty-program-client'), $this->buildGraphQLQuery(['tariff_id' => $this->_activeTariff->id]));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals('You need to add at least one active card to create a subscription.', $response->json('errors.0.extensions.validation.cards.0'));
    }

    protected function buildGraphQLQuery(array $variables = []): array
    {
        return [
            'query' => <<<GRAPHQL
                mutation CreateSubscription(\$tariff_id: Int!) {
                    createSubscription(tariff_id: \$tariff_id) {
                        {$this->convertIntoQuery($this->_structure)}
                    }
                }
            GRAPHQL,
            'variables' => $variables,
        ];
    }

    private function assertSuccessCreateSubscriptionResponse(TestResponse $response, array $variables): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure(['data' => ['createSubscription' => $this->_structure]]);

        $subscription = $this->_activeUser->subscriptions()->first();
        $this->assertNotNull($subscription);
        $this->assertEquals($variables['tariff_id'], $subscription->tariff_id);
        $this->assertEquals($subscription->remaining_limit_amount, $this->_activeTariff->limit_amount);

        $this->assertEquals($subscription->id, $response->json('data.createSubscription.id'));
        $this->assertEquals($subscription->is_active_period, $response->json('data.createSubscription.is_active'));
        $this->assertEquals($subscription->remaining_limit_amount, $response->json('data.createSubscription.remaining_limit_amount'));
        $this->assertEquals($subscription->tariff_id, $response->json('data.createSubscription.tariff_id'));
        $this->assertEquals($subscription->user_id, $response->json('data.createSubscription.user_id'));
        $this->assertEquals($subscription->expired_at->timestamp, $response->json('data.createSubscription.expired_at'));
        $this->assertEquals($subscription->created_at->timestamp, $response->json('data.createSubscription.created_at'));
        $this->assertEquals($subscription->updated_at->timestamp, $response->json('data.createSubscription.updated_at'));

        // Tariff
        $this->assertEquals($subscription->tariff->id, $response->json('data.createSubscription.tariff.id'));
        $this->assertEquals($subscription->tariff->name_uz, $response->json('data.createSubscription.tariff.name_uz'));
        $this->assertEquals($subscription->tariff->name_ru, $response->json('data.createSubscription.tariff.name_ru'));
        $this->assertEquals($subscription->tariff->amount, $response->json('data.createSubscription.tariff.amount'));
        $this->assertEquals($subscription->tariff->free_months, $response->json('data.createSubscription.tariff.free_months'));
        $this->assertEquals($subscription->tariff->subscription_months, $response->json('data.createSubscription.tariff.subscription_months'));
        $this->assertEquals($subscription->tariff->is_free_for_previous_subscribers, $response->json('data.createSubscription.tariff.is_free_for_previous_subscribers'));
        $this->assertEquals($subscription->tariff->limit_amount, $response->json('data.createSubscription.tariff.limit_amount'));
        $this->assertEquals($subscription->tariff->is_active, $response->json('data.createSubscription.tariff.is_active'));
        $this->assertEquals($subscription->tariff->created_at->timestamp, $response->json('data.createSubscription.tariff.created_at'));
        $this->assertEquals($subscription->tariff->updated_at->timestamp, $response->json('data.createSubscription.tariff.updated_at'));
        $this->assertEquals($subscription->tariff->deleted_at?->timestamp, $response->json('data.createSubscription.tariff.deleted_at'));
    }
}
