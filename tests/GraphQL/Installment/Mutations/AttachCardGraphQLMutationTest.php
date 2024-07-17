<?php

namespace GraphQL\Installment\Mutations;

use App\Enums\Company\CardStatusEnum;
use App\Models\Company\Card;
use App\Models\Company\Installment;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class AttachCardGraphQLMutationTest extends GraphQLTestCase
{
    private const GRAPHQL_ROUTE = 'graphql.installment';

    protected bool $tenancy = true;

    private Card $_card;
    private Installment $_installment;

    protected function setUp(): void
    {
        parent::setUp();
        Installment::query()->forceDelete();
        Card::query()->forceDelete();

        $this->_card = Card::factory()->create(['status' => CardStatusEnum::ACTIVE]);
        $this->_installment = Installment::factory()->create();
    }

    #[Test]
    public function successAttach(): void
    {
        $response = $this->sendGraphQLRequest(
            installment_id: $this->_installment->id,
            card_id: $this->_card->id,
        );

        $response->assertOk();
        $response->assertJsonIsObject();

        $response->assertJsonStructure(
            [
                'data' => [
                    'attachCard',
                ],
            ]
        );
    }

    #[Test]
    public function errorDuplicateAttachCard()
    {
        $this->successAttach();
        $response = $this->sendGraphQLRequest(
            installment_id: $this->_installment->id,
            card_id: $this->_card->id,
        );

        $response->assertJsonIsObject();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertStringContainsString('validation', $response->json('errors.0.message'));
        $this->assertEquals(
            trans('validation.unique', ['attribute' => 'card id']),
            $response->json('errors.0.extensions.validation')['card_id'][0]
        );
    }

    #[Test]
    public function errorValidation(): void
    {
        $response = $this->sendGraphQLRequest(9999, 6556);
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertStringContainsString('validation', $response->json('errors.0.message'));
        $this->assertEquals(
            trans('validation.exists', ['attribute' => 'installment id']),
            $response->json('errors.0.extensions.validation')['installment_id'][0]
        );
        $this->assertEquals(
            trans('validation.exists', ['attribute' => 'card id']),
            $response->json('errors.0.extensions.validation')['card_id'][0]
        );
    }

    private function sendGraphQLRequest(int $installment_id, ?int $card_id): TestResponse
    {
        return $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route(self::GRAPHQL_ROUTE),
                $this->buildGraphQLQuery($installment_id, $card_id)
            );
    }

    private function buildGraphQLQuery(int $installment_id, ?int $card_id): array
    {
        return [
            'variables' => ['installment_id' => $installment_id, 'card_id' => $card_id],
            'query' => '
            mutation ($installment_id: Int!, $card_id: Int!) {
                attachCard(installment_id: $installment_id, card_id: $card_id)
            }
        ',
        ];
    }
}
