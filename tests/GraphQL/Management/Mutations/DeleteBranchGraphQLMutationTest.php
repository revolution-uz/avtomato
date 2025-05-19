<?php

namespace Tests\GraphQL\Management\Mutations;

use App\Models\Company\Branch;
use App\Models\Company\Installment;
use App\Models\Company\Transaction;
use Illuminate\Support\Arr;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class DeleteBranchGraphQLMutationTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    protected function setUp(): void
    {
        parent::setUp();

        Branch::factory()->count(3)->create();
        Installment::factory()->count(3)->create();
        Transaction::factory()->count(3)->create();
    }

    #[Test]
    public function successDeleteBranch()
    {
        $idsInput = Branch::query()->doesntHave('installments')->doesntHave('transactions')->inRandomOrder()->limit(3)->pluck('id')->toArray();

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery(['ids' => $idsInput])
            );

        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'deleteBranch',
            ],
        ]);

        $this->assertTrue($response->json('data.deleteBranch'));
        $this->assertTrue(Branch::query()->whereIn('id', $idsInput)->doesntExist());
    }

    #[Test]
    public function errorArgumentsValidation()
    {
        $idsInput = [null];
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery(['ids' => $idsInput])
            );
        $response->assertJsonStructure([
            'errors' => [
                [
                    'message',
                    'extensions' => [
                        'validation' => [
                            'ids.0',
                        ],
                    ],
                ],
            ],
        ]);
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.required', ['attribute' => 'ids.0']), $response->json('errors.0.extensions.validation')['ids.0'][0]);

        $idsInput = ['elkfmerk'];
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery(['ids' => $idsInput])
            );
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === "Variable \"\$ids\" got invalid value \"{$idsInput[0]}\" at \"ids[0]\"; Int cannot represent non-integer value: \"{$idsInput[0]}\""));

        $idsInput = [Branch::query()->orderByDesc('id')->first()->id + 1];
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery(['ids' => $idsInput])
            );
        $response->assertJsonStructure([
            'errors' => [
                [
                    'message',
                    'extensions' => [
                        'validation' => [
                            'ids.0',
                        ],
                    ],
                ],
            ],
        ]);
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'ids.0']), $response->json('errors.0.extensions.validation')['ids.0'][0]);
    }

    #[Test]
    public function errorWhenInstallmentsExist()
    {
        $idsInput = Branch::query()->has('installments')->inRandomOrder()->limit(1)->pluck('id')->toArray();

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery(['ids' => $idsInput])
            );

        $response->assertJsonStructure([
            'errors' => [
                [
                    'message',
                    'extensions' => [
                        'validation' => [
                            'ids.0',
                        ],
                    ],
                ],
            ],
        ]);
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('api.cannot_delete', ['item' => 'api.Branch', 'element' => 'api.Installment']), $response->json('errors.0.extensions.validation')['ids.0'][0]);
    }

    #[Test]
    public function errorWhenTransactionsExist()
    {
        $idsInput = Branch::query()->has('transactions')->inRandomOrder()->limit(1)->pluck('id')->toArray();

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.management'), $this->buildGraphQLQuery(['ids' => $idsInput]));

        $response->assertJsonStructure([
            'errors' => [
                [
                    'message',
                    'extensions' => [
                        'validation' => [
                            'ids.0',
                        ],
                    ],
                ],
            ],
        ]);
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('api.cannot_delete', ['item' => 'api.Branch', 'element' => 'api.Transaction']), $response->json('errors.0.extensions.validation')['ids.0'][0]);
    }

    private function buildGraphQLQuery(array $variables = []): array
    {
        $query = [];
        $query['variables'] = $variables;
        $query['query'] = '
            mutation DeleteBranch($ids: [Int]!) {
                deleteBranch(ids: $ids)
            }
        ';

        return $query;
    }
}
