<?php

namespace Tests\GraphQL\Scoring\Mutations;

use App\Enums\Company\CompanyStatusEnum;
use App\Enums\Company\Scoring\ScoringRequestTypeEnum;
use App\Enums\FeatureEnum;
use App\Models\CentralScoringCard;
use App\Models\Company\BalanceCharge;
use App\Models\Company\Card;
use App\Models\Company\Customer;
use App\Models\Company\Feature;
use App\Models\Company\ScoringRequest;
use Illuminate\Support\Arr;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\Fakers\UniredFaker;
use Tests\GraphQL\GraphQLTestCase;

class CreateCardScoringGraphQLMutationTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private int $_featureAmount = 65;
    private array $_humoInput = ['number' => '9860353506066262', 'expiry' => '2804', 'phone' => '998901234567'];
    private array $_uzCardInput = ['number' => '8600353506066262', 'expiry' => '3004'];
    private array $_structure = [
        'send_otp',
        'card_id',
        'phone_mask',
        'scoring_id',
    ];

    protected function setUp(): void
    {
        parent::setUp();
        CentralScoringCard::query()->forceDelete();
        Customer::query()->forceDelete();
        Card::query()->forceDelete();
        ScoringRequest::query()->forceDelete();
        Feature::query()->whereIn('name', [FeatureEnum::SCORING_HUMO, FeatureEnum::SCORING_UZCARD])->update(['amount' => $this->_featureAmount]);
    }

    #[Test]
    public function successHumoCardCreate(): void
    {
        UniredFaker::monitoringHumo();
        CentralScoringCard::factory()->create(['number' => $this->_humoInput['number'], 'is_verified' => true]);

        $variables = [
            ...$this->_humoInput,
            'type' => ScoringRequestTypeEnum::HUMO->value,
        ];

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.scoring'), $this->buildGraphQLQuery($variables));

        $this->assertSuccessCreateHumoCardScoringResponse($response);

        $balanceCharge = BalanceCharge::query()->latest()->firstOrFail();
        $feature = Feature::query()->where('name', FeatureEnum::SCORING_HUMO)->firstOrFail();

        $this->assertEquals($feature->id, $balanceCharge->feature_id);
        $this->assertEquals($this->_featureAmount, $balanceCharge->amount);
        $this->assertEquals($response->json('data.createCardScoring.scoring_id'), $balanceCharge->chargeable_id);
        $this->assertEquals(ScoringRequest::class, $balanceCharge->chargeable_type);
    }

    #[Test]
    public function successUzCardCreate(): void
    {
        UniredFaker::monitoringUzcard();
        CentralScoringCard::factory()->create(['number' => $this->_uzCardInput['number'], 'is_verified' => true]);

        $variables = [
            ...$this->_uzCardInput,
            'type' => ScoringRequestTypeEnum::UZCARD->value,
        ];

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.scoring'), $this->buildGraphQLQuery($variables));

        $this->assertSuccessCreateUzCardScoringResponse($response);

        $balanceCharge = BalanceCharge::query()->latest()->firstOrFail();
        $feature = Feature::query()->where('name', FeatureEnum::SCORING_UZCARD)->firstOrFail();

        $this->assertEquals($feature->id, $balanceCharge->feature_id);
        $this->assertEquals($this->_featureAmount, $balanceCharge->amount);
        $this->assertEquals($response->json('data.createCardScoring.scoring_id'), $balanceCharge->chargeable_id);
        $this->assertEquals(ScoringRequest::class, $balanceCharge->chargeable_type);
    }

    #[Test]
    public function errorHumoCardCreate(): void
    {
        CentralScoringCard::factory()->create(['number' => $this->_humoInput['number'], 'is_verified' => true]);
        $this->mockUrl('https://autopaygroup.uz/api/v1/partners', json_decode(file_get_contents(storage_path('../JSON-for-testing/scoring/unired-failed-response.json')), true));
        $variables = [
            ...$this->_humoInput,
            'type' => ScoringRequestTypeEnum::HUMO->value,
        ];

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.scoring'), $this->buildGraphQLQuery($variables));

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(__('api.unknown_error'), $response->json('errors.0.extensions.validation.otp.0'));
        $this->assertTrue(BalanceCharge::query()->doesntExist());
    }

    #[Test]
    public function errorUzCardCreate(): void
    {
        CentralScoringCard::factory()->create(['number' => $this->_uzCardInput['number'], 'is_verified' => true]);
        $this->mockUrl('https://autopaygroup.uz/api/v1/partners', json_decode(file_get_contents(storage_path('../JSON-for-testing/scoring/unired-failed-response.json')), true));
        $variables = [
            ...$this->_uzCardInput,
            'type' => ScoringRequestTypeEnum::UZCARD->value,
        ];

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.scoring'), $this->buildGraphQLQuery($variables));

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(__('api.unknown_error'), $response->json('errors.0.extensions.validation.otp.0'));
        $this->assertTrue(BalanceCharge::query()->doesntExist());
    }

    #[Test]
    public function errorValidation(): void
    {
        $variables = [
            'number' => '9860353506066262',
            'expiry' => 1231,
            'type' => ScoringRequestTypeEnum::HUMO->value,
        ];
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.scoring'), $this->buildGraphQLQuery($variables));
        $response->assertOk();
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$expiry" got invalid value 1231; String cannot represent a non string value: 1231'));

        $variables = [
            'number' => '9860353506066262',
            'expiry' => '0000',
            'type' => ScoringRequestTypeEnum::KATM_CREDIT_HISTORY->value,
        ];
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.scoring'), $this->buildGraphQLQuery($variables));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.in', ['attribute' => 'type']), $response->json('errors.0.extensions.validation.type.0'));

        $variables = [
            'number' => '315039531300',
            'expiry' => '123',
            'type' => ScoringRequestTypeEnum::HUMO->value,
        ];
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.scoring'), $this->buildGraphQLQuery($variables));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.digits', ['attribute' => 'data.number', 'digits' => 16]), $response->json('errors.0.extensions.validation')['data.number'][0]);
        $this->assertEquals(trans('validation.digits', ['attribute' => 'data.expiry', 'digits' => 4]), $response->json('errors.0.extensions.validation')['data.expiry'][0]);
        $this->assertEquals(trans('validation.date_format', ['attribute' => 'data.expiry', 'format' => 'ym']), $response->json('errors.0.extensions.validation')['data.expiry'][1]);
        $this->assertEquals(trans('validation.required_if', ['attribute' => 'data.phone', 'other' => 'type', 'value' => 6]), $response->json('errors.0.extensions.validation')['data.phone'][0]);

        $variables = [
            'number' => '9860353506066262',
            'expiry' => '0428',
            'phone' => '998901234567',
            'type' => ScoringRequestTypeEnum::HUMO->value,
        ];
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.scoring'), $this->buildGraphQLQuery($variables));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.date_format', ['attribute' => 'data.expiry', 'format' => 'ym']), $response->json('errors.0.extensions.validation')['data.expiry'][0]);
    }

    #[Test]
    public function errorCreateWithInactiveCompany(): void
    {
        tenant()->updateOrFail(['status' => CompanyStatusEnum::INACTIVE]);

        $variables = [
            ...$this->_humoInput,
            'type' => ScoringRequestTypeEnum::HUMO->value,
        ];
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.scoring'), $this->buildGraphQLQuery($variables));

        $response->assertOk();
        $this->assertEquals(__('api.inactive_company'), $response->json('errors.0.message'));
    }

    private function buildGraphQLQuery(array $variables = []): array
    {
        return [
            'query' => <<<GRAPHQL
                mutation CreateCardScoring(
                    \$number: BigInteger
                    \$expiry: String!
                    \$phone: String
                    \$type: Int!
                ) {
                    createCardScoring(
                        data: { number: \$number, expiry: \$expiry, phone: \$phone }
                        type: \$type
                    ) {
                        {$this->convertIntoQuery($this->_structure)}
                    }
                }
            GRAPHQL,
            'variables' => $variables,
        ];
    }

    private function assertSuccessCreateUzCardScoringResponse(TestResponse $response): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure(['data' => ['createCardScoring' => $this->_structure]]);

        $scoringRequest = ScoringRequest::query()->firstOrFail();

        $this->assertEquals($scoringRequest->id, $response->json('data.createCardScoring.scoring_id'));
        $this->assertEquals(ScoringRequestTypeEnum::UZCARD, $scoringRequest->type);
        $this->assertEquals($this->_uzCardInput['number'], $scoringRequest->request->number);
        $this->assertEquals($this->_uzCardInput['expiry'], $scoringRequest->request->expiry);
    }

    private function assertSuccessCreateHumoCardScoringResponse($response): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure(['data' => ['createCardScoring' => $this->_structure]]);

        $scoringRequest = ScoringRequest::query()->firstOrFail();

        $this->assertEquals($scoringRequest->id, $response->json('data.createCardScoring.scoring_id'));
        $this->assertEquals(ScoringRequestTypeEnum::HUMO, $scoringRequest->type);
        $this->assertEquals($this->_humoInput['number'], $scoringRequest->request->number);
        $this->assertEquals($this->_humoInput['expiry'], $scoringRequest->request->expiry);
    }
}
