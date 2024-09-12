<?php

namespace Tests\GraphQL\Scoring\Mutations;

use App\Enums\Company\CompanyStatusEnum;
use App\Enums\Company\Scoring\ScoringRequestStatusEnum;
use App\Enums\Company\Scoring\ScoringRequestTypeEnum;
use App\Enums\FeatureEnum;
use App\Models\Company\BalanceCharge;
use App\Models\Company\District;
use App\Models\Company\Feature;
use App\Models\Company\Region;
use App\Models\Company\ScoringRequest;
use Illuminate\Support\Carbon;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class CreateKatmScoringGraphQLMutationTest extends GraphQLTestCase
{
    private int $_featureAmount = 100;
    private ScoringRequestTypeEnum $_type = ScoringRequestTypeEnum::KATM_INPS_HISTORY;

    protected function setUp(): void
    {
        parent::setUp();
        ScoringRequest::query()->forceDelete();
        $this->artisan('db:seed', ['--class' => '\\Database\\Seeders\\Company\\LocationSeeder']);
        Feature::query()->whereIn('name', [FeatureEnum::SCORING_INPS_HISTORY])->update(['amount' => $this->_featureAmount]);
    }

    #[Test]
    public function successCreate(): void
    {
        $this->mockUrl('https://api.abrand.uz/partner/katm/create-request*', json_decode(file_get_contents(storage_path('../JSON-for-testing/scoring/katm-create-request-response.json')), true));
        $response = $this->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.scoring'),
                $this->buildGraphQLQuery($this->input(), $this->_type)
            );
        $this->assertSuccessCreateKatmScoringResponse($response);

        $balanceCharge = BalanceCharge::query()->latest()->firstOrFail();
        $feature = Feature::query()->where('name', FeatureEnum::SCORING_INPS_HISTORY)->firstOrFail();

        $this->assertEquals($feature->id, $balanceCharge->feature_id);
        $this->assertEquals($this->_featureAmount, $balanceCharge->amount);
        $this->assertEquals($response->json('data.createKatmScoring'), $balanceCharge->chargeable_id);
        $this->assertEquals(ScoringRequest::class, $balanceCharge->chargeable_type);
    }

    #[Test]
    public function errorCreate(): void
    {
        $this->mockUrl('https://api.abrand.uz/partner/katm/create-request*', json_decode(file_get_contents(storage_path('../JSON-for-testing/scoring/katm-create-request-failed-response.json')), true));
        $response = $this->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.scoring'),
                $this->buildGraphQLQuery($this->input(), $this->_type)
            );
        $this->assertSuccessCreateKatmScoringResponse($response, ScoringRequestStatusEnum::FAILED);

        $this->assertTrue(BalanceCharge::query()->doesntExist());
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

    #[Test]
    public function errorCreateWithInactiveCompany(): void
    {
        tenant()->updateOrFail(['status' => CompanyStatusEnum::INACTIVE]);

        $response = $this->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.scoring'),
                $this->buildGraphQLQuery($this->input(), $this->_type)
            );

        $response->assertOk();
        $this->assertEquals(__('api.inactive_company'), $response->json('errors.0.message'));
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

    private function buildGraphQLQuery(array $data, ScoringRequestTypeEnum $type): array
    {
        return [
            'variables' => ['data' => $data, 'type' => $type->value],
            'query' => '
                mutation CreateKatmScoring($data: KatmCreateInputGraphQLType!, $type: Int!) {
                    createKatmScoring(data: $data, type: $type)
                }
            ',
        ];
    }

    private function assertSuccessCreateKatmScoringResponse(TestResponse $response, ScoringRequestStatusEnum $status = ScoringRequestStatusEnum::WAITING): void
    {
        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                'createKatmScoring',
            ],
        ]);

        $scoringRequest = ScoringRequest::query()->firstOrFail();

        $this->assertEquals($scoringRequest->id, $response->json('data.createKatmScoring'));
        $this->assertEquals($this->_type, $scoringRequest->type);
        $this->assertEquals($status, $scoringRequest->status);
    }
}
