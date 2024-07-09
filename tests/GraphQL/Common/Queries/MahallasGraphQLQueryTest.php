<?php

namespace Common\Queries;

use App\Models\Company\District;
use App\Models\Company\Mahalla;
use App\Models\Company\Region;
use Database\Seeders\Company\LocationSeeder;
use Illuminate\Support\Arr;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class MahallasGraphQLQueryTest extends GraphQLTestCase
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
    public function mahallas()
    {
        $response = $this
            ->postJson(
                route('graphql.common'),
                $this->buildGraphQLQuery()
            );

        $this->assertMahallasResponse($response);
    }

    #[Test]
    public function mahallasWithLanguage()
    {
        $locale = Arr::first(config('app.supported_locales'), fn (string $lang) => $lang !== config('app.locale'));
        $response = $this
            ->postJson(
                route('graphql.common'),
                $this->buildGraphQLQuery(),
                ['Accept-Language' => $locale]
            );
        app()->setLocale($locale);
        $this->assertMahallasResponse($response);
    }

    #[Test]
    public function successMahallasWithArguments()
    {
        $district = District::query()->with('mahallas')->first();

        $response = $this
            ->postJson(
                route('graphql.common'),
                $this->buildGraphQLQuery($district->id)
            );

        $this->assertEquals($district->mahallas()->count(), count($response->json('data.mahallas')));

        $this->assertEquals($district->id, $response->json('data.mahallas.0.district.id'));
        $this->assertEquals($district->name, $response->json('data.mahallas.0.district.name'));

        $mahalla = $district->mahallas()->first();

        $this->assertEquals($mahalla->id, $response->json('data.mahallas.0.id'));
        $this->assertEquals($mahalla->district_id, $response->json('data.mahallas.0.district_id'));
        $this->assertEquals($mahalla->name, $response->json('data.mahallas.0.name'));
    }

    #[Test]
    public function errorMahallasWithArguments()
    {
        $district = District::query()->orderByDesc('id')->first();

        $response = $this
            ->postJson(
                route('graphql.common'),
                $this->buildGraphQLQuery($district->id + 1)
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

    private function buildGraphQLQuery(?int $district_id = null): array
    {
        $query = [];
        $query['variables'] = $district_id ? ['district_id' => $district_id] : [];
        $query['query'] = '
            query Mahallas($district_id: Int) {
                mahallas(district_id: $district_id) {
                    id
                    district_id
                    name
                    district {
                        id
                        region_id
                        name
                    }
                }
            }
        ';

        return $query;
    }

    private function assertMahallasResponse(TestResponse $response): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'mahallas' => [
                    '*' => [
                        'id',
                        'district_id',
                        'name',
                        'district' => [
                            'id',
                            'region_id',
                            'name',
                        ],
                    ],
                ],
            ],
        ]);

        $mahalla = Mahalla::query()->with('district')->first();

        $this->assertEquals(Mahalla::query()->count(), count($response->json('data.mahallas')));

        $this->assertEquals($mahalla->id, $response->json('data.mahallas.0.id'));
        $this->assertEquals($mahalla->name, $response->json('data.mahallas.0.name'));

        $this->assertEquals($mahalla->district->id, $response->json('data.mahallas.0.district.id'));
        $this->assertEquals($mahalla->district->region_id, $response->json('data.mahallas.0.district.region_id'));
        $this->assertEquals($mahalla->district->name, $response->json('data.mahallas.0.district.name'));

    }
}
