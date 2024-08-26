<?php

namespace Tests\GraphQL\Customer\Queries;

use Database\Seeders\Company\LocationSeeder;
use Illuminate\Support\Facades\Artisan;
use PHPUnit\Framework\Attributes\Test;
use Tests\Fakers\PlumFaker;
use Tests\GraphQL\GraphQLTestCase;

class InfoByPassportGraphQLQueryTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('tenants:seed', [
            '--class' => LocationSeeder::class,
            '--tenants' => [tenant()->id],
        ]);
    }

    #[Test]
    public function successInfoByPassport()
    {
        PlumFaker::getInfoByPassport();

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.customer'),
                $this->buildGraphQLQuery([
                    'passport_series' => 'AD',
                    'passport_number' => '3119359',
                    'birth_date' => '15-03-1995',
                ])
            );

        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'infoByPassport' => [
                    'pinfl',
                    'tin',
                    'passport_series',
                    'passport_number',
                    'passport_given_date',
                    'passport_expiration_date',
                    'passport_given_place',
                    'passport_given_org',
                    'passport_citizenship_id',
                    'passport_citizenship',
                    'passport_living_place',
                    'first_name',
                    'last_name',
                    'middle_name',
                    'full_name',
                    'birth_date',
                    'birth_place',
                    'gender',
                    'address' => [
                        'address',
                        'region_id',
                        'district_id',
                        'mahalla_id',
                        'region' => [
                            'id',
                            'name',
                        ],
                        'district' => [
                            'id',
                            'region_id',
                            'name',
                        ],
                        'mahalla',
                    ],
                ],
            ],
        ]);
    }

    private function buildGraphQLQuery(array $variables = []): array
    {
        $query = [];
        $query['variables'] = $variables;
        $query['query'] = '
            query InfoByPassport(
                $passport_series: String!
                $passport_number: String!
                $birth_date: String!
            ) {
                infoByPassport(
                    passport_series: $passport_series
                    passport_number: $passport_number
                    birth_date: $birth_date
                ) {
                    pinfl
                    tin
                    passport_series
                    passport_number
                    passport_given_date
                    passport_expiration_date
                    passport_given_place
                    passport_given_org
                    passport_citizenship_id
                    passport_citizenship
                    passport_living_place
                    first_name
                    last_name
                    middle_name
                    full_name
                    birth_date
                    birth_place
                    gender
                    address {
                        address
                        region_id
                        district_id
                        mahalla_id
                        region {
                            id
                            name
                        }
                        district {
                            id
                            region_id
                            name
                        }
                        mahalla {
                            id
                            district_id
                            name
                        }
                    }
                }
            }
        ';

        return $query;
    }
}
