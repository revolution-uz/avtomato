<?php

namespace Tests\GraphQL\Scoring\Mutations;

use App\Enums\Company\CompanyStatusEnum;
use App\Enums\Company\Scoring\ScoringRequestStatusEnum;
use App\Enums\Company\Scoring\ScoringRequestTypeEnum;
use App\Enums\FeatureEnum;
use App\Models\Company\BalanceCharge;
use App\Models\Company\Feature;
use App\Models\Company\ScoringRequest;
use Database\Seeders\Company\LocationSeeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use PHPUnit\Framework\Attributes\Test;
use Tests\Fakers\ScoringFaker;
use Tests\GraphQL\GraphQLTestCase;

class CreateRoyxatCreditHistoryScoringGraphQLMutationTest extends GraphQLTestCase
{
    private int $_featureAmount = 100;

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('tenants:seed', ['--class' => LocationSeeder::class, '--tenants' => [tenant('id')]]);
        Feature::query()->whereIn('name', [FeatureEnum::SCORING_ROYXAT_CREDIT_HISTORY])->update(['amount' => $this->_featureAmount]);
    }

    #[Test]
    public function successCreate(): void
    {
        ScoringFaker::royxatLoansQuery();

        $response = $this->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.scoring'),
                $this->buildGraphQLQuery(['passport_id' => 'AA2234334'])
            );

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                'createRoyxatCreditHistoryScoring',
            ],
        ]);
        $this->assertIsInt($response->json('data.createRoyxatCreditHistoryScoring'));

        $scoringRequest = ScoringRequest::query()->findOrFail($response->json('data.createRoyxatCreditHistoryScoring'));
        $this->assertEquals(ScoringRequestTypeEnum::ROYXAT_CREDIT_HISTORY, $scoringRequest->type);
        $this->assertEquals(ScoringRequestStatusEnum::SUCCESS, $scoringRequest->status);

        $balanceCharge = BalanceCharge::query()->latest()->firstOrFail();
        $feature = Feature::query()->where('name', FeatureEnum::SCORING_ROYXAT_CREDIT_HISTORY)->firstOrFail();
        $this->assertEquals($feature->id, $balanceCharge->feature_id);
        $this->assertEquals($this->_featureAmount, $balanceCharge->amount);
        $this->assertEquals($scoringRequest->id, $balanceCharge->chargeable_id);
        $this->assertEquals(ScoringRequest::class, $balanceCharge->chargeable_type);
    }

    #[Test]
    public function createWithFailedStatus(): void
    {
        ScoringFaker::royxatLoansQuery(true);

        $response = $this->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.scoring'),
                $this->buildGraphQLQuery(['pinfl' => '01234567891012'])
            );

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                'createRoyxatCreditHistoryScoring',
            ],
        ]);
        $this->assertIsInt($response->json('data.createRoyxatCreditHistoryScoring'));

        $scoringRequest = ScoringRequest::query()->findOrFail($response->json('data.createRoyxatCreditHistoryScoring'));
        $this->assertEquals(ScoringRequestTypeEnum::ROYXAT_CREDIT_HISTORY, $scoringRequest->type);
        $this->assertEquals(ScoringRequestStatusEnum::FAILED, $scoringRequest->status);

        $this->assertTrue(
            BalanceCharge::query()
                ->where(['chargeable_id' => $scoringRequest->id, 'chargeable_type' => ScoringRequest::class])
                ->doesntExist()
        );
    }

    #[Test]
    public function errorValidation(): void
    {
        // required errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.scoring'),
                $this->buildGraphQLQuery(['passport_id' => null, 'pinfl' => null])
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertStringContainsString('validation', $response->json('errors.0.message'));
        $this->assertEquals(
            trans('validation.required_without', ['attribute' => 'data.passport id', 'values' => 'data.pinfl']),
            $response->json('errors.0.extensions.validation')['data.passport_id'][0]
        );
        $this->assertEquals(
            trans('validation.required_without', ['attribute' => 'data.pinfl', 'values' => 'data.passport id']),
            $response->json('errors.0.extensions.validation')['data.pinfl'][0]
        );

        // data type errors
        $response = $this->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.scoring'),
                $this->buildGraphQLQuery(['passport_id' => 123, 'pinfl' => 'abc'])
            );
        $this->assertNotNull(
            Arr::first(
                $response->json('errors'),
                fn ($item) => $item['message'] === 'Variable "$passport_id" got invalid value 123; String cannot represent a non string value: 123'
            )
        );
        $this->assertNotNull(
            Arr::first(
                $response->json('errors'),
                fn ($item) => $item['message'] === 'Variable "$pinfl" got invalid value "abc"; abc cannot represent a big integer.'
            )
        );

        // rule errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.scoring'),
                $this->buildGraphQLQuery(['passport_id' => '012345678', 'pinfl' => '012345678912'])
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertStringContainsString('validation', $response->json('errors.0.message'));
        $this->assertEquals(
            trans('validation.regex', ['attribute' => 'data.passport id']),
            $response->json('errors.0.extensions.validation')['data.passport_id'][0]
        );
        $this->assertEquals(
            trans('validation.digits', ['attribute' => 'data.pinfl', 'digits' => 14]),
            $response->json('errors.0.extensions.validation')['data.pinfl'][0]
        );
    }

    #[Test]
    public function errorCreateWithInactiveCompany(): void
    {
        tenant()->updateOrFail(['status' => CompanyStatusEnum::INACTIVE]);

        $response = $this->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.scoring'),
                $this->buildGraphQLQuery(['passport_id' => 'AA2234334'])
            );

        $response->assertOk();
        $this->assertEquals(__('api.inactive_company'), $response->json('errors.0.message'));
    }

    private function buildGraphQLQuery(array $variables = []): array
    {
        $query = [];
        $query['variables'] = $variables;
        $query['query'] = '
            mutation CreateRoyxatCreditHistoryScoring(
                $passport_id: String
                $pinfl: BigInteger
            ) {
                createRoyxatCreditHistoryScoring(
                    data: { pinfl: $pinfl, passport_id: $passport_id }
                )
            }
        ';

        return $query;
    }
}
