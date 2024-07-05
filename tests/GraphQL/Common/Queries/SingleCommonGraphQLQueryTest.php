<?php

namespace Common\Queries;

use App\Models\Company\District;
use App\Models\Company\Mahalla;
use App\Models\Company\Region;
use Tests\GraphQL\GraphQLTestCase;

class SingleCommonGraphQLQueryTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    protected function setUp(): void
    {
        parent::setUp();

        District::query()->forceDelete();
        District::factory()->count(1)->create();
        Mahalla::query()->forceDelete();
        Mahalla::factory()->count(1)->create();
    }

    #[Test]
    public function districts()
    {
        $region = Region::query()->first();
        $query = '
            query ($id: Int!) {
                districts(region_id: $id) {
	            	id
	            	name
	            	region_id
	            }
            }
        ';

        $response = $this
            ->postJson(
                route('graphql.common'),
                $this->buildGraphQLQuery($query, ['id' => $region->id])
            );

        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'districts' => [
                    '*' => [
                        'id',
                        'name',
                    ],
                ],
            ],
        ]);
        $this->assertEquals($region->id, $response->json('data.districts.0.region_id'));
    }

    #[Test]
    public function mahallas()
    {
        $district = District::query()->whereHas('mahallas')->first();
        $query = '
            query ($id: Int!) {
            	mahallas (district_id: $id) {
            		id
            		name
            		district_id
            	}
            }
        ';

        $response = $this
            ->postJson(
                route('graphql.common'),
                $this->buildGraphQLQuery($query, ['id' => $district->id])
            );

        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'mahallas' => [
                    '*' => [
                        'id',
                        'name',
                        'district_id',
                    ],
                ],
            ],
        ]);
        $this->assertEquals($district->id, $response->json('data.mahallas.0.district_id'));
    }

    private function buildGraphQLQuery(string $q, array $variables): array
    {
        $query = [];
        $query['variables'] = $variables;
        $query['query'] = $q;

        return $query;
    }
}
