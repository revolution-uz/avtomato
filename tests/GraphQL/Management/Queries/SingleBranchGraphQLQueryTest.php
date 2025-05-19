<?php

namespace Tests\GraphQL\Management\Queries;

use App\Models\Company\Branch;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class SingleBranchGraphQLQueryTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private array $_structure = [
        'id',
        'name',
        'address',
        'inn',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        Branch::factory()->count(30)->create();
    }

    #[Test]
    public function successWithId()
    {
        $branch = Branch::query()->first();

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery(['id' => $branch->id])
            );

        $this->assertSuccessSingleBranchResponse($response, $branch);
    }

    #[Test]
    public function errorInvalidId()
    {
        $id = Branch::query()->orderByDesc('id')->first()->id;
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery(['id' => $id + 1])
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
    }

    #[Test]
    public function errorValidationId()
    {
        $id = 'hjebfwef';
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery(['id' => $id])
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals("Variable \"\$id\" got invalid value \"{$id}\"; Int cannot represent non-integer value: \"{$id}\"", $response->json('errors.0.message'));
    }

    private function buildGraphQLQuery(array $variables = []): array
    {
        $query = [];
        $query['variables'] = $variables;
        $query['query'] = '
            query SingleBranch($id: Int!) {
                singleBranch(id: $id) {' . $this->convertIntoQuery($this->_structure) . '}
            }
        ';

        return $query;
    }

    private function assertSuccessSingleBranchResponse(TestResponse $response, ?Branch $branch): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'singleBranch' => $this->_structure,
            ],
        ]);

        $this->assertNotNull($branch);
        $this->assertEquals($branch->id, $response->json('data.singleBranch.id'));
        $this->assertEquals($branch->name, $response->json('data.singleBranch.name'));
        $this->assertEquals($branch->address, $response->json('data.singleBranch.address'));
        $this->assertEquals($branch->inn, $response->json('data.singleBranch.inn'));
        $this->assertEquals($branch->created_at->timestamp, $response->json('data.singleBranch.created_at'));
        $this->assertEquals($branch->updated_at?->timestamp, $response->json('data.singleBranch.updated_at'));
        $this->assertEquals($branch->deleted_at?->timestamp, $response->json('data.singleBranch.deleted_at'));

    }
}
