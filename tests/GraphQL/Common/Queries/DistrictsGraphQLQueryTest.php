<?php

namespace Common\Queries;

use App\Models\Company\District;
use App\Models\Company\Mahalla;
use App\Models\Company\Region;
use Database\Seeders\Company\LocationSeeder;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class DistrictsGraphQLQueryTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    protected function setUp(): void
    {
        parent::setUp();

        Region::query()->forceDelete();
        District::query()->forceDelete();
        Mahalla::query()->forceDelete();
        $this->artisan('db:seed', ['--class' => LocationSeeder::class]);
    }

    #[Test]
    public function districts()
    {
        $response = $this
            ->postJson(
                route('graphql.common'),
                $this->buildGraphQLQuery()
            );

        $this->assertDistrictsResponse($response);
    }

    #[Test]
    public function districtsWithLanguage()
    {
        $response = $this
            ->postJson(
                route('graphql.common'),
                $this->buildGraphQLQuery()
            );
        app()->setLocale(config('app.locale'));
        $this->assertDistrictsResponse($response);

        foreach (config('app.supported_locales') as $locale) {
            $response = $this
                ->postJson(
                    route('graphql.common'),
                    $this->buildGraphQLQuery(),
                    ['Accept-Language' => $locale]
                );
            app()->setLocale($locale);
            $this->assertDistrictsResponse($response);
        }
    }

    #[Test]
    public function successDistrictsWithArguments()
    {
        $region = Region::query()->with('districts')->first();

        $response = $this
            ->postJson(
                route('graphql.common'),
                $this->buildGraphQLQuery($region->id)
            );

        $this->assertEquals($region->districts()->count(), count($response->json('data.districts')));

        $this->assertEquals($region->id, $response->json('data.districts.0.region.id'));
        $this->assertEquals($region->name, $response->json('data.districts.0.region.name'));

        $district = $region->districts()->first();

        $this->assertEquals($district->mahallas()->count(), count($response->json('data.districts.0.mahallas')));

        $mahalla = $district->mahallas()->first();

        $this->assertEquals($mahalla->id, $response->json('data.districts.0.mahallas.0.id'));
        $this->assertEquals($mahalla->district_id, $response->json('data.districts.0.mahallas.0.district_id'));
        $this->assertEquals($mahalla->name, $response->json('data.districts.0.mahallas.0.name'));
    }

    #[Test]
    public function errorDistrictsWithArguments()
    {
        $region = Region::query()->orderByDesc('id')->first();

        $response = $this
            ->postJson(
                route('graphql.common'),
                $this->buildGraphQLQuery($region->id + 1)
            );

        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'errors' => [
                '*' => [
                    'message',
                ],
            ],
        ]);
        $this->assertEquals($response->json('errors.0.message'), 'validation');
    }

    private function buildGraphQLQuery(?int $region_id = null): array
    {
        $query = [];
        $query['variables'] = $region_id ? ['region_id' => $region_id] : [];
        $query['query'] = '
            query Districts($region_id: Int) {
                districts(region_id: $region_id) {
                    id
                    region_id
                    name
                    region {
                        id
                        name
                    }
                    mahallas {
                        id
                        district_id
                        name
                    }
                }
            }
        ';

        return $query;
    }

    private function assertDistrictsResponse(TestResponse $response): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'districts' => [
                    '*' => [
                        'id',
                        'region_id',
                        'name',
                        'region' => [
                            'id',
                            'name',
                        ],
                        'mahallas' => [
                            '*' => [
                                'id',
                                'district_id',
                                'name',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $district = District::query()->with(['region', 'mahallas'])->first();

        $this->assertEquals(District::query()->count(), count($response->json('data.districts')));

        $this->assertEquals($district->id, $response->json('data.districts.0.id'));
        $this->assertEquals($district->name, $response->json('data.districts.0.name'));

        $this->assertEquals($district->region->id, $response->json('data.districts.0.region.id'));
        $this->assertEquals($district->region->district_id, $response->json('data.districts.0.region.district_id'));
        $this->assertEquals($district->region->name, $response->json('data.districts.0.region.name'));

        $this->assertEquals($district->mahallas()->count(), count($response->json('data.districts.0.mahallas')));

        $mahalla = $district->mahallas()->first();

        $this->assertEquals($mahalla->id, $response->json('data.districts.0.mahallas.0.id'));
        $this->assertEquals($mahalla->district_id, $response->json('data.districts.0.mahallas.0.district_id'));
        $this->assertEquals($mahalla->name, $response->json('data.districts.0.mahallas.0.name'));
    }
}
