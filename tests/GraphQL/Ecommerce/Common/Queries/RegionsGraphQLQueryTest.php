<?php

namespace Tests\GraphQL\Ecommerce\Common\Queries;

use App\Models\Company\Region;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class RegionsGraphQLQueryTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private int $_totalRegionsCount = 29;

    private array $_structure = [
        'id',
        'name',
        'name_uz',
        'name_oz',
        'name_ru',
        'name_en',
        'districts' => [
            '*' => [
                'id',
                'name',
                'name_uz',
                'name_oz',
                'name_ru',
                'name_en',
                'region_id',
            ],
        ],
    ];

    protected function setUp(): void
    {
        parent::setUp();

        Region::query()->forceDelete();
        Region::factory()->count($this->_totalRegionsCount)->create();
    }

    #[Test]
    public function successRegions(): void
    {
        $response = $this->postJson(
            route('graphql.ecommerce-common'), $this->buildGraphQLQuery()
        );

        $this->assertSuccessRegionsResponse($response);
    }

    private function buildGraphQLQuery(array $variables = []): array
    {
        return [
            'query' => <<<GRAPHQL
                query Regions {
                    regions {
                        {$this->convertIntoQuery($this->_structure)}
                    }
                }
            GRAPHQL,
            'variables' => $variables,
        ];
    }

    private function assertSuccessRegionsResponse(TestResponse $response, $total = null): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure(['data' => ['regions' => ['*' => $this->_structure]]]);

        $totalCount = $total ?? $this->_totalRegionsCount;
        $this->assertCount($totalCount, $response->json('data.regions'));
    }
}
