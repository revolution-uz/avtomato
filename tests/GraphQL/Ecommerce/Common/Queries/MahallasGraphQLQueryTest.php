<?php

namespace GraphQL\Ecommerce\Common\Queries;

use App\Models\Company\District;
use App\Models\Company\Mahalla;
use Illuminate\Support\Arr;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class MahallasGraphQLQueryTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private int $_totalMahallasCount = 29;

    private array $_structure = [
        'id',
        'name',
        'name_uz',
        'name_oz',
        'name_ru',
        'name_en',
        'district_id',
        'district' => [
            'id',
            'name',
            'name_uz',
            'name_oz',
            'name_ru',
            'name_en',
            'region_id',
        ],
    ];

    protected function setUp(): void
    {
        parent::setUp();

        Mahalla::query()->forceDelete();
        Mahalla::factory()->count($this->_totalMahallasCount)->create();
    }

    #[Test]
    public function successWithoutArguments(): void
    {
        $response = $this->postJson(
            route('graphql.ecommerce-common'), $this->buildGraphQLQuery()
        );

        $this->assertSuccessMahallasResponse($response);
    }

    #[Test]
    public function successWithDistrictId()
    {
        $region = District::factory()->create();
        Mahalla::factory()->count(5)->create(['district_id' => $region->id]);

        $response = $this->postJson(
            route('graphql.ecommerce-common'), $this->buildGraphQLQuery(['district_id' => $region->id])
        );

        $this->assertSuccessMahallasResponse($response, 5);
    }

    #[Test]
    public function errorValidation()
    {
        // GraphQL type errors
        $response = $this->postJson(
            route('graphql.ecommerce-common'), $this->buildGraphQLQuery(['district_id' => 'qwerty'])
        );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$district_id" got invalid value "qwerty"; Int cannot represent non-integer value: "qwerty"'));

        // Rule errors
        $response = $this->postJson(
            route('graphql.ecommerce-common'), $this->buildGraphQLQuery(['district_id' => 999999999])
        );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'district id']), $response->json('errors.0.extensions.validation.district_id.0'));
    }

    private function buildGraphQLQuery(array $variables = []): array
    {
        return [
            'query' => <<<GRAPHQL
                query Mahallas(\$district_id: Int) {
                    mahallas(district_id: \$district_id) {
                        {$this->convertIntoQuery($this->_structure)}
                    }
                }
            GRAPHQL,
            'variables' => $variables,
        ];
    }

    private function assertSuccessMahallasResponse(TestResponse $response, $total = null): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure(['data' => ['mahallas' => ['*' => $this->_structure]]]);

        $totalCount = $total ?? $this->_totalMahallasCount;
        $this->assertCount($totalCount, $response->json('data.mahallas'));
    }
}
