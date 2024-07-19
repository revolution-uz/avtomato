<?php

namespace Installment\Mutations;

use App\Models\Company\InstallmentComment;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class UpdateInstallmentCommentGraphQLMutationTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    protected function setUp(): void
    {
        parent::setUp();

        InstallmentComment::factory()->count(10)->create();
    }

    #[Test]
    public function successUpdateInstallmentComment()
    {
        $installmentComment = InstallmentComment::query()->inRandomOrder()->first();

        $installmentInput = [
            'installment_id' => $installmentComment->installment_id,
            'comment_id' => $installmentComment->id,
            'comment' => Str::random(10),
        ];

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery($installmentInput)
            );

        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'updateInstallmentComment' => [
                    'id',
                    'installment_id',
                    'admin_id',
                    'comment',
                    'created_at',
                    'updated_at',
                    'deleted_at',
                ],
            ],
        ]);

        $installmentComment->refresh();
        $this->assertEquals($installmentComment->comment, $installmentInput['comment']);

        $this->assertEquals($installmentComment->id, $response->json('data.updateInstallmentComment.id'));
        $this->assertEquals($installmentComment->installment_id, $response->json('data.updateInstallmentComment.installment_id'));
        $this->assertEquals($installmentComment->admin_id, $response->json('data.updateInstallmentComment.admin_id'));
        $this->assertEquals($installmentComment->comment, $response->json('data.updateInstallmentComment.comment'));
        $this->assertEquals($installmentComment->created_at->timestamp, $response->json('data.updateInstallmentComment.created_at'));
        $this->assertEquals($installmentComment->updated_at->timestamp, $response->json('data.updateInstallmentComment.updated_at'));
        $this->assertNull($response->json('data.updateInstallmentComment.deleted_at'));
    }

    #[Test]
    public function errorValidation(): void
    {
        // GraphQL type errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery([
                    'installment_id' => null,
                    'comment_id' => null,
                    'comment' => null,
                ])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotNull(Arr::first($response->json('errors'), fn($item) => $item['message'] === 'Variable "$installment_id" of non-null type "Int!" must not be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn($item) => $item['message'] === 'Variable "$comment_id" of non-null type "Int!" must not be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn($item) => $item['message'] === 'Variable "$comment" of non-null type "String!" must not be null.'));

        // rule errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery([
                    'installment_id' => 999999999,
                    'comment_id' => 999999999,
                    'comment' => Str::random(10),
                ])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'installment id']), $response->json('errors.0.extensions.validation.installment_id.0'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'comment id']), $response->json('errors.0.extensions.validation.comment_id.0'));
    }

    private function buildGraphQLQuery(array $variables = []): array
    {
        $query = [];
        $query['variables'] = $variables;
        $query['query'] = '
            mutation UpdateInstallmentComment(
                $installment_id: Int!
                $comment_id: Int!
                $comment: String!
            ) {
                updateInstallmentComment(
                    installment_id: $installment_id
                    comment_id: $comment_id
                    comment: $comment
                ) {
                    id
                    installment_id
                    admin_id
                    comment
                    created_at
                    updated_at
                    deleted_at
                }
            }
        ';

        return $query;
    }
}
