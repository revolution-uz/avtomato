<?php

namespace Tests\GraphQL\Management\Mutations;

use App\Enums\Company\LimitBranchEnum;
use App\Models\Company\Branch;
use App\Models\Company\Module;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class CreateBranchGraphQLMutationTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    #[Test]
    public function successCreateBranch()
    {
        Module::where('name', 'like', 'multibranch%')->update(['is_active' => false]);
        Module::where('name', 'multibranch_2')->update(['is_active' => true]);
        $branchInput = [
            'name' => Str::random(255),
            'address' => Str::random(255),
            'inn' => Str::random(9),
        ];

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery($branchInput)
            );

        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'createBranch' => [
                    'id',
                    'name',
                    'address',
                    'inn',
                    'created_at',
                    'updated_at',
                    'deleted_at',
                ],
            ],
        ]);

        $branch = Branch::query()->find($response->json('data.createBranch.id'));
        $this->assertNotNull($branch);

        $this->assertEquals($branch->name, $branchInput['name']);
        $this->assertEquals($branch->address, $branchInput['address']);
        $this->assertEquals($branch->inn, $branchInput['inn']);

        $this->assertEquals($branch->name, $response->json('data.createBranch.name'));
        $this->assertEquals($branch->address, $response->json('data.createBranch.address'));
        $this->assertEquals($branch->inn, $response->json('data.createBranch.inn'));
        $this->assertEquals($branch->created_at->timestamp, $response->json('data.createBranch.created_at'));
        $this->assertEquals($branch->updated_at->timestamp, $response->json('data.createBranch.updated_at'));
        $this->assertNull($response->json('data.createBranch.deleted_at'));
    }

    #[Test]
    public function errorArgumentsValidation()
    {
        $branchInput = [
            'name' => null,
            'address' => null,
            'inn' => null,
        ];
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery($branchInput)
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$name" of non-null type "String!" must not be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$address" of non-null type "String!" must not be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$inn" of non-null type "String!" must not be null.'));

        $branchInput = [
            'name' => Str::random(256),
            'address' => Str::random(256),
            'inn' => Str::random(10),
        ];
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery($branchInput)
            );
        $response->assertOk();
        $response->assertJsonStructure([
            'errors' => [
                [
                    'message',
                    'extensions' => [
                        'validation' => [
                            'name',
                            'address',
                            'inn',
                        ],
                    ],
                ],
            ],
        ]);
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'Имя', 'max' => 255]), $response->json('errors.0.extensions.validation.name.0'));
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'address', 'max' => 255]), $response->json('errors.0.extensions.validation.address.0'));
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'inn', 'max' => 9]), $response->json('errors.0.extensions.validation.inn.0'));
    }

    #[Test]
    public function limitMultibranch1(): void
    {
        $this->runMultibranchLimitTest('multibranch_1', LimitBranchEnum::MULTIBRANCH_1_BRANCH->value);
    }

    #[Test]
    public function limitMultibranch2(): void
    {
        Branch::factory()->create();  // Create 1 branch
        $this->runMultibranchLimitTest('multibranch_2', LimitBranchEnum::MULTIBRANCH_2_BRANCH->value);
    }

    #[Test]
    public function limitMultibranch3(): void
    {
        Branch::factory(2)->create();  // Create 2 branches
        $this->runMultibranchLimitTest('multibranch_3', LimitBranchEnum::MULTIBRANCH_3_BRANCH->value);
    }

    #[Test]
    public function limitMultibranch4(): void
    {
        Branch::factory(4)->create();  // Create 4 branches
        $this->runMultibranchLimitTest('multibranch_4', LimitBranchEnum::MULTIBRANCH_4_BRANCH->value);
    }

    private function runMultibranchLimitTest(string $moduleName, int $branchLimit): void
    {
        Module::where('name', 'like', 'multibranch%')->update(['is_active' => false]);
        Module::where('name', $moduleName)->update(['is_active' => true]);

        $branchInput = [
            'name' => Str::random(255),
            'address' => Str::random(255),
            'inn' => Str::random(9),
        ];

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery($branchInput)
            );

        $this->assertEquals(__('api.n_branches', ['n' => $branchLimit]), $response->json('errors.0.extensions.debugMessage'));
    }

    private function buildGraphQLQuery(array $variables = []): array
    {
        $query = [];
        $query['variables'] = $variables;
        $query['query'] = '
            mutation CreateBranch($name: String!, $address: String!, $inn: String!) {
                createBranch(name: $name, address: $address, inn: $inn) {
                    id
                    name
                    address
                    inn
                    created_at
                    updated_at
                    deleted_at
                }
            }
        ';

        return $query;
    }
}
