<?php

namespace GraphQL\Installment\Mutations;

use App\Models\Company\Installment;
use App\Models\Company\InstallmentComment;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class CreateInstallmentCommentGraphQLMutationTest extends GraphQLTestCase
{
    private const GRAPHQL_ROUTE = 'graphql.installment';

    protected bool $tenancy = true;

    private string $_comment = 'This is a comment';

    protected function setUp(): void
    {
        parent::setUp();
        Installment::query()->forceDelete();
        InstallmentComment::query()->forceDelete();
    }

    #[Test]
    public function successComment()
    {
        $installment = Installment::factory()->create();
        $response = $this->sendGraphQLRequest(
            installment_id: $installment->id,
            comment: $this->_comment,
        );
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure(
            [
                'data' => [
                    'createInstallmentComment' => [
                        'id',
                        'installment_id',
                        'installment' => [
                            'id',
                        ],
                        'admin_id',
                        'admin' => [
                            'id',
                        ],
                        'comment',
                        'created_at',
                        'updated_at',
                        'deleted_at',
                    ],
                ],
            ]
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
    }

    private function sendGraphQLRequest(int $installment_id, string $comment): TestResponse
    {
        return $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route(self::GRAPHQL_ROUTE),
                $this->buildGraphQLQuery($installment_id, $comment)
            );
    }

    private function buildGraphQLQuery(int $installment_id, string $comment): array
    {
        return [
            'variables' => ['installment_id' => $installment_id, 'comment' => $comment],
            'query' => '
            mutation ($installment_id: Int!, $comment: String!) {
                createInstallmentComment(installment_id: $installment_id, comment: $comment){
                    id
                    installment_id
                    installment {
                        id
                    }
                    admin_id
                    admin {
                        id
                    }
                    comment
                    created_at
                    updated_at
                    deleted_at
                }
            }
        ',
        ];
    }
}
