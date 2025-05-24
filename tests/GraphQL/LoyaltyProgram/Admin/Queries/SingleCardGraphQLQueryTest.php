<?php

namespace Tests\GraphQL\LoyaltyProgram\Admin\Queries;

use App\Models\Company\LoyaltyProgram\Card;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class SingleCardGraphQLQueryTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private array $_structure = [
        'id',
        'number',
        'expiry',
        'owner',
        'phone',
        'status',
        'balance',
        'user_id',
        'created_at',
        'updated_at',
        'card_type',
        'is_connected',
        'user' => [
            'id',
            'phone',
            'full_name',
            'first_name',
            'last_name',
            'birth_date',
            'created_at',
            'updated_at',
            'deleted_at',
            'has_available_subscription',
        ],
    ];

    protected function setUp(): void
    {
        parent::setUp();

        Card::query()->forceDelete();
        Card::factory()->count(30)->create();
    }

    #[Test]
    public function successWithId()
    {
        $card = Card::query()->first();

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.loyalty-program-admin'), $this->buildGraphQLQuery(['id' => $card->id]));

        $this->assertSuccessSingleCardResponse($response, $card);
    }

    #[Test]
    public function errorInvalidId()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.loyalty-program-admin'), $this->buildGraphQLQuery(['id' => 999999999]));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
    }

    #[Test]
    public function errorValidationId()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.loyalty-program-admin'), $this->buildGraphQLQuery(['id' => 'not-an-integer']));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('Variable "$id" got invalid value "not-an-integer"; Int cannot represent non-integer value: "not-an-integer"', $response->json('errors.0.message'));
    }

    #[Test]
    public function successWithDeletedUser()
    {
        $card = Card::query()->first();
        $card->user->delete();
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.loyalty-program-admin'), $this->buildGraphQLQuery(['id' => $card->id]));
        $this->assertSuccessSingleCardResponse($response, $card);
        $this->assertEquals($card->user->id, $response->json('data.singleCard.user.id'));
        $this->assertNotNull($response->json('data.singleCard.user.deleted_at'));
    }

    private function buildGraphQLQuery(array $variables = []): array
    {
        return [
            'query' => <<<GRAPHQL
                query SingleCard(\$id: Int!) {
                    singleCard(id: \$id) {
                        {$this->convertIntoQuery($this->_structure)}
                    }
                }
            GRAPHQL,
            'variables' => $variables,
        ];
    }

    private function assertSuccessSingleCardResponse(TestResponse $response, Card $card): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure(['data' => ['singleCard' => $this->_structure]]);

        $this->assertEquals($card->id, $response->json('data.singleCard.id'));
        $this->assertEquals($card->pan, $response->json('data.singleCard.number'));
        $this->assertEquals($card->expiry, $response->json('data.singleCard.expiry'));
        $this->assertEquals($card->owner, $response->json('data.singleCard.owner'));
        $this->assertEquals($card->phone, $response->json('data.singleCard.phone'));
        $this->assertEquals($card->status->value, $response->json('data.singleCard.status'));
        $this->assertEquals($card->balance, $response->json('data.singleCard.balance'));
        $this->assertEquals($card->user_id, $response->json('data.singleCard.user_id'));
        $this->assertEquals($card->created_at->timestamp, $response->json('data.singleCard.created_at'));
        $this->assertEquals($card->updated_at->timestamp, $response->json('data.singleCard.updated_at'));
        $this->assertEquals($card->card_type, $response->json('data.singleCard.card_type'));
        $this->assertEquals($card->is_connected, $response->json('data.singleCard.is_connected'));

        // User
        $this->assertEquals($card->user->id, $response->json('data.singleCard.user.id'));
        $this->assertEquals($card->user->phone, $response->json('data.singleCard.user.phone'));
        $this->assertEquals($card->user->full_name, $response->json('data.singleCard.user.full_name'));
        $this->assertEquals($card->user->first_name, $response->json('data.singleCard.user.first_name'));
        $this->assertEquals($card->user->last_name, $response->json('data.singleCard.user.last_name'));
        $this->assertEquals($card->user->birth_date?->timestamp, $response->json('data.singleCard.user.birth_date'));
        $this->assertEquals($card->user->created_at->timestamp, $response->json('data.singleCard.user.created_at'));
        $this->assertEquals($card->user->updated_at->timestamp, $response->json('data.singleCard.user.updated_at'));
        $this->assertEquals($card->user->deleted_at?->timestamp, $response->json('data.singleCard.user.deleted_at'));
        $this->assertEquals($card->user->has_available_subscription, $response->json('data.singleCard.user.has_available_subscription'));
    }
}
