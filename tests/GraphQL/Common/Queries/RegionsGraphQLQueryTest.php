<?php

namespace Tests\GraphQL\Common\Queries;

use App\Models\Company\District;
use App\Models\Company\Mahalla;
use App\Models\Company\Region;
use Database\Seeders\Company\LocationSeeder;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class RegionsGraphQLQueryTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private array $_languages = ['en', 'ru', 'uz', 'oz'];

    protected function setUp(): void
    {
        parent::setUp();

        Region::query()->forceDelete();
        District::query()->forceDelete();
        Mahalla::query()->forceDelete();
        $this->artisan('db:seed', ['--class' => LocationSeeder::class]);
    }

    #[Test]
    public function regions()
    {
        $response = $this
            ->postJson(
                route('graphql.common'),
                $this->buildGraphQLQuery()
            );

        $this->assertRegionsResponse($response);
    }

    #[Test]
    public function regionsWithLanguage()
    {
        $response = $this
            ->postJson(
                route('graphql.common'),
                $this->buildGraphQLQuery()
            );
        app()->setLocale(config('app.locale'));
        $this->assertRegionsResponse($response);

        foreach (config('app.supported_locales') as $locale) {
            $response = $this
                ->postJson(
                    route('graphql.common'),
                    $this->buildGraphQLQuery(),
                    ['Accept-Language' => $locale]
                );
            app()->setLocale($locale);
            $this->assertRegionsResponse($response);
        }
    }

    private function buildGraphQLQuery(): array
    {
        $query = [];
        $query['variables'] = [];
        $query['query'] = '
            query Regions {
                regions {
                    id
                    name
                    districts {
                        id
                        region_id
                        name
                    }
                }
            }
        ';

        return $query;
    }

    private function assertRegionsResponse(TestResponse $response): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'regions' => [
                    '*' => [
                        'id',
                        'name',
                        'districts' => [
                            '*' => [
                                'id',
                                'region_id',
                                'name',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $region = Region::query()->with('districts')->first();

        $this->assertEquals(Region::query()->count(), count($response->json('data.regions')));

        $this->assertEquals($region->id, $response->json('data.regions.0.id'));
        $this->assertEquals($region->name, $response->json('data.regions.0.name'));

        $this->assertEquals($region->districts()->count(), count($response->json('data.regions.0.districts')));

        $district = $region->districts()->first();

        $this->assertEquals($district->id, $response->json('data.regions.0.districts.0.id'));
        $this->assertEquals($district->region_id, $response->json('data.regions.0.districts.0.region_id'));
        $this->assertEquals($district->name, $response->json('data.regions.0.districts.0.name'));
    }
}
