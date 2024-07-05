<?php

namespace Common\Queries;

use App\Models\Company\District;
use App\Models\Company\Mahalla;
use App\Models\Company\Region;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class CommonGraphQLQueryTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private int $_totalFactoryCount = 30;

    protected function setUp(): void
    {
        parent::setUp();

        District::query()->forceDelete();
        District::factory()->count($this->_totalFactoryCount)->create();
        Mahalla::query()->forceDelete();
        Mahalla::factory()->count($this->_totalFactoryCount)->create();
        Region::query()->forceDelete();
        Region::factory()->count($this->_totalFactoryCount)->create();
    }

    #[Test]
    public function successGettingDistricts()
    {
        $query = '
            query {
            	districts {
            		id
            		name
            		region {
            			id
            			name
            		}
            	}
            }
        ';

        $response = $this
            ->postJson(
                route('graphql.common'),
                $this->buildGraphQLQuery($query)
            );

        $this->assertSuccessCommonsResponse($response, [
            'data' => [
                'districts' => [
                    '*' => [
                        'id',
                        'name',
                        'region' => [
                            'id',
                            'name',
                        ],
                    ],
                ],
            ],
        ]);
    }

    #[Test]
    public function successGettingMahallas()
    {
        $query = '
            query {
	            mahallas {
	            	id
	            	name
	            }
            }
        ';

        $response = $this
            ->postJson(
                route('graphql.common'),
                $this->buildGraphQLQuery($query)
            );

        $this->assertSuccessCommonsResponse($response, [
            'data' => [
                'mahallas' => [
                    '*' => [
                        'id',
                        'name',
                    ],
                ],
            ],
        ]);
    }

    #[Test]
    public function successGettingRegions()
    {
        $query = '
            query{
                regions {
	            	id
	            	name
	            	districts {
	            		id
	            		name
	            	}
	            }
	        }
        ';

        $response = $this
            ->postJson(
                route('graphql.common'),
                $this->buildGraphQLQuery($query)
            );

        $this->assertSuccessCommonsResponse($response, [
            'data' => [
                'regions' => [
                    '*' => [
                        'id',
                        'name',
                        'districts' => [
                            '*' => [
                                'id',
                                'name',
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }

    #[Test]
    public function successGettingSettings()
    {
        $query = '
            query {
            	settings {
            		app_maintenance_mode
            		app_min_version
            		balance
            		web_maintenance_mode
            	}
            }
        ';

        $response = $this
            ->postJson(
                route('graphql.common'),
                $this->buildGraphQLQuery($query)
            );

        $this->assertSuccessCommonsResponse($response, [
            'data' => [
                'settings' => [
                    'app_maintenance_mode',
                    'app_min_version',
                    'balance',
                    'web_maintenance_mode',
                ],
            ],
        ]);
    }

    private function buildGraphQLQuery(string $q, array $variables = []): array
    {
        $query = [];
        $query['variables'] = $variables;
        $query['query'] = $q;

        return $query;
    }

    private function assertSuccessCommonsResponse(TestResponse $response, array $structure): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure($structure);
    }
}
