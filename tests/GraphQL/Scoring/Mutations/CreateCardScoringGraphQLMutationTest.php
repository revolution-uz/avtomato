<?php

namespace Tests\GraphQL\Scoring\Mutations;

use App\Enums\Company\CompanyStatusEnum;
use App\Enums\Company\Scoring\ScoringRequestTypeEnum;
use App\Enums\FeatureEnum;
use App\Models\Company\BalanceCharge;
use App\Models\Company\Card;
use App\Models\Company\Customer;
use App\Models\Company\Feature;
use App\Models\Company\ScoringRequest;
use Illuminate\Support\Arr;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class CreateCardScoringGraphQLMutationTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private int $_featureAmount = 65;
    private array $_humoInput = ['number' => '9860353506066262', 'expiry' => '0430'];
    private array $_uzCardInput = ['number' => '8600353506066262', 'expiry' => '0430'];

    protected function setUp(): void
    {
        parent::setUp();
        Customer::query()->forceDelete();
        Card::query()->forceDelete();
        ScoringRequest::query()->forceDelete();
        Feature::query()->whereIn('name', [FeatureEnum::SCORING_HUMO, FeatureEnum::SCORING_UZCARD])->update(['amount' => $this->_featureAmount]);
    }

    #[Test]
    public function successHumoCardCreate(): void
    {
        $this->mockUrl('https://api.abrand.uz/partner/katm/humo*', json_decode(file_get_contents(storage_path('../JSON-for-testing/scoring/katm-humo-response.json')), true));

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.scoring'),
                $this->buildGraphQLQuery(data: $this->_humoInput, type: ScoringRequestTypeEnum::HUMO)
            );

        $this->assertSuccessCreateHumoCardScoringResponse($response);

        $balanceCharge = BalanceCharge::query()->latest()->firstOrFail();
        $feature = Feature::query()->where('name', FeatureEnum::SCORING_HUMO)->firstOrFail();

        $this->assertEquals($feature->id, $balanceCharge->feature_id);
        $this->assertEquals($this->_featureAmount, $balanceCharge->amount);
        $this->assertEquals($response->json('data.createCardScoring'), $balanceCharge->chargeable_id);
        $this->assertEquals(ScoringRequest::class, $balanceCharge->chargeable_type);
    }

    #[Test]
    public function successUzCardCreate(): void
    {
        $this->mockUrl('https://api.abrand.uz/partner/katm/uzcard*', json_decode(file_get_contents(storage_path('../JSON-for-testing/scoring/katm-humo-response.json')), true));

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.scoring'),
                $this->buildGraphQLQuery(data: $this->_uzCardInput, type: ScoringRequestTypeEnum::UZCARD)
            );

        $this->assertSuccessCreateUzCardScoringResponse($response);

        $balanceCharge = BalanceCharge::query()->latest()->firstOrFail();
        $feature = Feature::query()->where('name', FeatureEnum::SCORING_UZCARD)->firstOrFail();

        $this->assertEquals($feature->id, $balanceCharge->feature_id);
        $this->assertEquals($this->_featureAmount, $balanceCharge->amount);
        $this->assertEquals($response->json('data.createCardScoring'), $balanceCharge->chargeable_id);
        $this->assertEquals(ScoringRequest::class, $balanceCharge->chargeable_type);
    }

    #[Test]
    public function errorHumoCardCreate(): void
    {
        $this->mockUrl('https://api.abrand.uz/partner/katm/humo*', json_decode(file_get_contents(storage_path('../JSON-for-testing/scoring/katm-humo-failed-response.json')), true));

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.scoring'),
                $this->buildGraphQLQuery(data: $this->_humoInput, type: ScoringRequestTypeEnum::HUMO)
            );

        $this->assertSuccessCreateHumoCardScoringResponse($response);
        $this->assertTrue(BalanceCharge::query()->doesntExist());
    }

    #[Test]
    public function errorUzCardCreate(): void
    {
        $this->mockUrl('https://api.abrand.uz/partner/katm/uzcard*', json_decode(file_get_contents(storage_path('../JSON-for-testing/scoring/katm-humo-failed-response.json')), true));

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.scoring'),
                $this->buildGraphQLQuery(data: $this->_uzCardInput, type: ScoringRequestTypeEnum::UZCARD)
            );

        $this->assertSuccessCreateUzCardScoringResponse($response);
        $this->assertTrue(BalanceCharge::query()->doesntExist());
    }

    #[Test]
    public function errorValidation(): void
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.scoring'),
                $this->buildGraphQLQuery(['number' => '9860353506066262', 'expiry' => 1231], ScoringRequestTypeEnum::HUMO)
            );
        $response->assertOk();
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$data" got invalid value 1231 at "data.expiry"; String cannot represent a non string value: 1231'));

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.scoring'),
                $this->buildGraphQLQuery(['number' => '9860353506066262', 'expiry' => '0000'], ScoringRequestTypeEnum::KATM_CREDIT_HISTORY)
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.in', ['attribute' => 'type']), $response->json('errors.0.extensions.validation')['type'][0]);
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.scoring'),
                $this->buildGraphQLQuery(['number' => '315039531300', 'expiry' => '123'], ScoringRequestTypeEnum::HUMO)
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.digits', ['attribute' => 'data.number', 'digits' => 16]), $response->json('errors.0.extensions.validation')['data.number'][0]);
        $this->assertEquals(trans('validation.digits', ['attribute' => 'data.expiry', 'digits' => 4]), $response->json('errors.0.extensions.validation')['data.expiry'][0]);
    }

    #[Test]
    public function errorCreateWithInactiveCompany(): void
    {
        tenant()->updateOrFail(['status' => CompanyStatusEnum::INACTIVE]);

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.scoring'),
                $this->buildGraphQLQuery(data: $this->_humoInput, type: ScoringRequestTypeEnum::HUMO)
            );

        $response->assertOk();
        $this->assertEquals(__('api.inactive_company'), $response->json('errors.0.message'));
    }

    private function buildGraphQLQuery(array $data, ScoringRequestTypeEnum $type): array
    {
        $query = [];
        $query['variables'] = ['data' => $data, 'type' => $type->value];
        $query['query'] = '
            mutation ($data: CardCreateInputGraphQLType!, $type: Int!) {
                createCardScoring(data: $data, type: $type)
            }
        ';

        return $query;
    }

    private function assertSuccessCreateUzCardScoringResponse(TestResponse $response): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'createCardScoring',
            ],
        ]);

        $scoringRequest = ScoringRequest::query()->firstOrFail();

        $this->assertEquals($scoringRequest->id, $response->json('data.createCardScoring'));
        $this->assertEquals(ScoringRequestTypeEnum::UZCARD, $scoringRequest->type);
        $this->assertEquals($this->_uzCardInput['number'], $scoringRequest->request->number);
        $this->assertEquals($this->_uzCardInput['expiry'], $scoringRequest->request->expiry);
    }

    private function assertSuccessCreateHumoCardScoringResponse($response): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'createCardScoring',
            ],
        ]);

        $scoringRequest = ScoringRequest::query()->firstOrFail();

        $this->assertEquals($scoringRequest->id, $response->json('data.createCardScoring'));
        $this->assertEquals(ScoringRequestTypeEnum::HUMO, $scoringRequest->type);
        $this->assertEquals($this->_humoInput['number'], $scoringRequest->request->number);
        $this->assertEquals($this->_humoInput['expiry'], $scoringRequest->request->expiry);
    }
}
