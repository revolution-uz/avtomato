<?php

namespace Installment\Mutations;

use App\Models\Company\InstallmentComment;
use Illuminate\Support\Arr;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class DeleteInstallmentCommentGraphQLMutationTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    protected function setUp(): void
    {
        parent::setUp();

        InstallmentComment::query()->forceDelete();
        InstallmentComment::factory()->count(5)->create();
    }

    #[Test]
    public function successDeleteInstallmentComment()
    {
        $idsInput = InstallmentComment::query()->inRandomOrder()->limit(3)->pluck('id')->toArray();

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery(['ids' => $idsInput])
            );

        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'deleteInstallmentComment',
            ],
        ]);

        $this->assertTrue($response->json('data.deleteInstallmentComment'));
        $this->assertTrue(InstallmentComment::query()->whereIn('id', $idsInput)->doesntExist());
    }

    #[Test]
    public function errorValidation(): void
    {
        // GraphQL type errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery(['ids' => null])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotNull(Arr::first($response->json('errors'), fn($item) => $item['message'] === 'Variable "$ids" of non-null type "[Int]!" must not be null.'));

        // rule errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery([
                    'ids' => [999999999],
                ])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('api.not found'), $response->json('errors.0.extensions.validation')['ids.0'][0]);
    }

    private function buildGraphQLQuery(array $variables = []): array
    {
        $query = [];
        $query['variables'] = $variables;
        $query['query'] = '
            mutation DeleteInstallmentComment($ids: [Int]!) {
                deleteInstallmentComment(ids: $ids)
            }
        ';

        return $query;
    }
}
