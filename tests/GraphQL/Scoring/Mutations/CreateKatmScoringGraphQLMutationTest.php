<?php

namespace Tests\GraphQL\Scoring\Mutations;

use App\Enums\Company\Scoring\ScoringRequestTypeEnum;
use App\Models\Company\District;
use App\Models\Company\Region;
use App\Models\Company\ScoringRequest;
use Illuminate\Support\Carbon;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class CreateKatmScoringGraphQLMutationTest extends GraphQLTestCase
{
    private int $_type = ScoringRequestTypeEnum::KATM_INPS_HISTORY->value;

    protected function setUp(): void
    {
        parent::setUp();
        ScoringRequest::query()->forceDelete();
        $this->artisan('db:seed', ['--class' => '\\Database\\Seeders\\Company\\LocationSeeder']);
    }

    #[Test]
    public function successCreate(): void
    {
        $this->mockUrl('https://api.abrand.uz/partner/katm/create-request*', json_decode(file_get_contents(storage_path('../JSON-for-testing/card-scoring-create-response.json')), true));
        $response = $this->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.scoring'),
                $this->buildGraphQLQuery($this->input(), $this->_type)
            );
        $this->assertSuccessCreateKatmScoringResponse($response);
    }

    #[Test]
    public function errorValidation(): void
    {
        $invalidData = $this->input();
        $invalidData['region_id'] = 0;
        $invalidData['district_id'] = 0;
        $invalidData['passport_given_date'] = 'invalid_date';
        $invalidData['birth_date'] = 'invalid_date';
        $invalidData['phone'] = 'invalid_phone';
        $invalidData['extra_phone'] = 'invalid_phone';
        $invalidData['passport_number'] = 'invalid_passport_number';
        $invalidData['passport_series'] = 'invalid_passport_series';
        $invalidData['pinfl'] = '1';
        $invalidData['first_name'] = 'invalid_first_name';
        $invalidData['last_name'] = 'invalid_last_name';
        $invalidData['middle_name'] = 'invalid_middle_name';

        $response = $this->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.scoring'),
                $this->buildGraphQLQuery($invalidData, $this->_type)
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertStringContainsString('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.digits', ['attribute' => 'data.pinfl', 'digits' => 14]), $response->json('errors.0.extensions.validation')['data.pinfl'][0]);
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'data.passport series', 'max' => 2]), $response->json('errors.0.extensions.validation')['data.passport_series'][0]);
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'data.passport number', 'max' => 7]), $response->json('errors.0.extensions.validation')['data.passport_number'][0]);
        $this->assertEquals(trans('validation.regex', ['attribute' => 'data.passport number']), $response->json('errors.0.extensions.validation')['data.passport_number'][1]);
        $this->assertEquals(trans('validation.date', ['attribute' => 'data.passport given date']), $response->json('errors.0.extensions.validation')['data.passport_given_date'][0]);
        $this->assertEquals(trans('validation.integer', ['attribute' => 'data.phone']), $response->json('errors.0.extensions.validation')['data.phone'][0]);
        $this->assertEquals(trans('validation.digits', ['attribute' => 'data.phone', 'digits' => 12]), $response->json('errors.0.extensions.validation')['data.phone'][1]);
        $this->assertEquals(trans('validation.integer', ['attribute' => 'data.extra phone']), $response->json('errors.0.extensions.validation')['data.extra_phone'][0]);
        $this->assertEquals(trans('validation.digits', ['attribute' => 'data.extra phone', 'digits' => 12]), $response->json('errors.0.extensions.validation')['data.extra_phone'][1]);
        $this->assertEquals(trans('validation.date', ['attribute' => 'data.birth date']), $response->json('errors.0.extensions.validation')['data.birth_date'][0]);
        $this->assertEquals(trans('validation.exists', ['attribute' => 'data.region id']), $response->json('errors.0.extensions.validation')['data.region_id'][0]);
        $this->assertEquals(trans('validation.exists', ['attribute' => 'data.district id']), $response->json('errors.0.extensions.validation')['data.district_id'][0]);
    }

    private function input(): array
    {
        $region = Region::query()->first();
        $district = District::query()->where('region_id', $region->id)->first();

        return [
            'pinfl' => '50203040506070',
            'passport_series' => 'AA',
            'passport_number' => '2234334',
            'passport_given_date' => Carbon::now()->format('d-m-Y'),
            'first_name' => 'Ismiddin',
            'last_name' => 'Drsaliev',
            'middle_name' => 'Sharipovich',
            'phone' => '998901234567',
            'extra_phone' => '998901234567',
            'birth_date' => Carbon::createFromFormat('Y-m-d', '1990-01-01')->format('d-m-Y'),
            'region_id' => $region->id,
            'district_id' => $district->id,
        ];
    }

    private function buildGraphQLQuery(array $data, int $type): array
    {
        return [
            'variables' => ['data' => $data, 'type' => $type],
            'query' => '
                mutation CreateKatmScoring($data: KatmCreateInputGraphQLType!, $type: Int!) {
                    createKatmScoring(data: $data, type: $type)
                }
            ',
        ];
    }

    private function assertSuccessCreateKatmScoringResponse(TestResponse $response): void
    {
        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                'createKatmScoring',
            ],
        ]);
        $scoringRequest = ScoringRequest::query()->find($response->json('data.createKatmScoring'));
        $this->assertNotNull($scoringRequest);
        $this->assertEquals($this->_type, $scoringRequest->type->value);
    }
}
