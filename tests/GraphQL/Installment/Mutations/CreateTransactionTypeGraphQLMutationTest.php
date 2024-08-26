<?php

namespace Tests\GraphQL\Installment\Mutations;

use App\Models\Company\TransactionType;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class CreateTransactionTypeGraphQLMutationTest extends GraphQLTestCase
{
    private const GRAPHQL_ROUTE = 'graphql.installment';

    protected bool $tenancy = true;

    private string $_type_name = 'Type name';

    protected function setUp(): void
    {
        parent::setUp();
        TransactionType::query()->forceDelete();
    }

    #[Test]
    public function successCreateTransactionType()
    {
        $response = $this->sendGraphQLRequest(
            $this->_type_name
        );
        $this->assertSuccessResponse($response);
    }

    #[Test]
    public function errorValidation(): void
    {
        $response = $this->sendGraphQLRequest(
            Str::random(44),
        );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertStringContainsString('validation', $response->json('errors.0.message'));
        $this->assertEquals(
            trans('validation.max.string', ['attribute' => trans('validation.attributes.name'), 'max' => 32]),
            $response->json('errors.0.extensions.validation')['name'][0]
        );
        $response = $this->sendGraphQLRequest('as');
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertStringContainsString('validation', $response->json('errors.0.message'));
        $this->assertEquals(
            trans('validation.min.string', ['attribute' => trans('validation.attributes.name'), 'min' => 3]),
            $response->json('errors.0.extensions.validation')['name'][0]
        );
    }

    private function sendGraphQLRequest($name): TestResponse
    {
        return $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route(self::GRAPHQL_ROUTE),
                $this->buildGraphQLQuery($name)
            );
    }

    private function buildGraphQLQuery($name): array
    {
        return [
            'variables' => ['name' => $name],
            'query' => '
            mutation ($name: String!) {
                createTransactionType(name: $name) {
                    id
                    name
                    created_at
                    updated_at
                }
            }
        ',
        ];
    }

    private function assertSuccessResponse(TestResponse $response): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'createTransactionType' => [
                    'id',
                    'name',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);
    }
}
