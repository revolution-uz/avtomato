<?php

namespace Tests\GraphQL\Ecommerce\Common\Queries;

use App\Models\Company\District;
use App\Models\Company\Region;
use Illuminate\Support\Arr;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class DistrictsGraphQLQueryTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private int $_totalDistrictsCount = 29;

    private array $_structure = [
        'id',
        'name',
        'name_uz',
        'name_oz',
        'name_ru',
        'name_en',
        'region_id',
        'region' => [
            'id',
            'name',
            'name_uz',
            'name_oz',
            'name_ru',
            'name_en',
        ],
        'mahallas' => [
            '*' => [
                'id',
                'name',
                'name_uz',
                'name_oz',
                'name_ru',
                'name_en',
                'district_id',
            ],
        ],
    ];

    protected function setUp(): void
    {
        parent::setUp();

        District::query()->forceDelete();
        District::factory()->count($this->_totalDistrictsCount)->create();
    }

    #[Test]
    public function successWithoutArguments(): void
    {
        $response = $this->postJson(
            route('graphql.ecommerce-common'), $this->buildGraphQLQuery()
        );

        $this->assertSuccessDistrictsResponse($response);
    }

    #[Test]
    public function successWithRegionId()
    {
        $region = Region::factory()->create();
        District::factory()->count(5)->create(['region_id' => $region->id]);

        $response = $this->postJson(
            route('graphql.ecommerce-common'), $this->buildGraphQLQuery(['region_id' => $region->id])
        );

        $this->assertSuccessDistrictsResponse($response, 5);
    }

    #[Test]
    public function errorValidation()
    {
        // GraphQL type errors
        $response = $this->postJson(
            route('graphql.ecommerce-common'), $this->buildGraphQLQuery(['region_id' => 'qwerty'])
        );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$region_id" got invalid value "qwerty"; Int cannot represent non-integer value: "qwerty"'));

        // Rule errors
        $response = $this->postJson(
            route('graphql.ecommerce-common'), $this->buildGraphQLQuery(['region_id' => 999999999])
        );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'region id']), $response->json('errors.0.extensions.validation.region_id.0'));
    }

    private function buildGraphQLQuery(array $variables = []): array
    {
        return [
            'query' => <<<GRAPHQL
                query Districts(\$region_id: Int) {
                    districts(region_id: \$region_id) {
                        {$this->convertIntoQuery($this->_structure)}
                    }
                }
            GRAPHQL,
            'variables' => $variables,
        ];
    }

    private function assertSuccessDistrictsResponse(TestResponse $response, $total = null): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure(['data' => ['districts' => ['*' => $this->_structure]]]);

        $totalCount = $total ?? $this->_totalDistrictsCount;
        $this->assertCount($totalCount, $response->json('data.districts'));
    }
}
