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
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class CreateDebtScoringGraphQLMutationTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private int $_featureAmount = 100;
    private string $_phone = '998990009988';
    private array $_input = ['pinfl' => '50203040506070'];
    private int $taxType = ScoringRequestTypeEnum::PLUM_TAX_INFO->value;
    private int $mibType = ScoringRequestTypeEnum::PLUM_MIB_INFO->value;

    protected function setUp(): void
    {
        parent::setUp();
        Customer::query()->forceDelete();
        Card::query()->forceDelete();
        ScoringRequest::query()->forceDelete();
        Feature::query()->whereIn('name', [FeatureEnum::SCORING_MIB_INFO, FeatureEnum::SCORING_TAX_INFO])->update(['amount' => $this->_featureAmount]);
    }

    #[Test]
    public function successTaxCreate(): void
    {
        $this->mockUrl('https://api.abrand.uz/partner/katm/tax*', ['result' => ['debtBal' => 1]]);
        $response = $this->sendGraphQLRequest($this->_input, $this->taxType);
        $this->assertSuccessCreateDebtScoringResponse($response, $this->taxType);

        $balanceCharge = BalanceCharge::query()->latest()->firstOrFail();
        $feature = Feature::query()->where('name', FeatureEnum::SCORING_TAX_INFO)->firstOrFail();

        $this->assertEquals($feature->id, $balanceCharge->feature_id);
        $this->assertEquals($this->_featureAmount, $balanceCharge->amount);
        $this->assertEquals($response->json('data.createDebtScoring'), $balanceCharge->chargeable_id);
        $this->assertEquals(ScoringRequest::class, $balanceCharge->chargeable_type);
    }

    #[Test]
    public function successMibCreate(): void
    {
        $this->mockUrl('https://api.abrand.uz/partner/katm/mib*', ['result' => ['debtBal' => 1]]);
        $inputWithPhone = array_merge($this->_input, ['phone' => $this->_phone]);
        $response = $this->sendGraphQLRequest($inputWithPhone, $this->mibType);
        $this->assertSuccessCreateDebtScoringResponse($response, $this->mibType);

        $balanceCharge = BalanceCharge::query()->latest()->firstOrFail();
        $feature = Feature::query()->where('name', FeatureEnum::SCORING_MIB_INFO)->firstOrFail();

        $this->assertEquals($feature->id, $balanceCharge->feature_id);
        $this->assertEquals($this->_featureAmount, $balanceCharge->amount);
        $this->assertEquals($response->json('data.createDebtScoring'), $balanceCharge->chargeable_id);
        $this->assertEquals(ScoringRequest::class, $balanceCharge->chargeable_type);
    }

    #[Test]
    public function errorTaxCreate(): void
    {
        $this->mockUrl('https://api.abrand.uz/partner/katm/tax*', ['result' => ['error' => 'FAIL']]);
        $response = $this->sendGraphQLRequest($this->_input, $this->taxType);
        $this->assertSuccessCreateDebtScoringResponse($response, $this->taxType);
        $this->assertTrue(BalanceCharge::query()->doesntExist());
    }

    #[Test]
    public function errorMibCreate(): void
    {
        $this->mockUrl('https://api.abrand.uz/partner/katm/mib*', ['result' => ['error' => 'FAIL']]);
        $inputWithPhone = array_merge($this->_input, ['phone' => $this->_phone]);
        $response = $this->sendGraphQLRequest($inputWithPhone, $this->mibType);
        $this->assertSuccessCreateDebtScoringResponse($response, $this->mibType);
        $this->assertTrue(BalanceCharge::query()->doesntExist());
    }

    #[Test]
    public function errorValidation(): void
    {
        $testData = [
            ['type' => $this->taxType, 'pinfl' => '123456789', 'phone' => '1234567'],
            ['type' => $this->mibType, 'pinfl' => '123456789', 'phone' => '1234567'],
        ];

        foreach ($testData as $data) {
            $response = $this->sendGraphQLRequest([
                'pinfl' => $data['pinfl'],
                'phone' => $data['phone'],
            ], $data['type']);

            $response->assertOk();
            $this->assertArrayHasKey('errors', $response->json());
            $this->assertStringContainsString('validation', $response->json('errors.0.message'));
            $this->assertValidationErrors($response);
        }
    }

    #[Test]
    public function errorCreateWithInactiveCompany(): void
    {
        tenant()->updateOrFail(['status' => CompanyStatusEnum::INACTIVE]);
        $response = $this->sendGraphQLRequest($this->_input, $this->taxType);
        $response->assertOk();
        $this->assertEquals(__('api.inactive_company'), $response->json('errors.0.message'));
    }

    private function sendGraphQLRequest(array $data, int $type): TestResponse
    {
        return $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.scoring'), $this->buildGraphQLQuery($data, $type));
    }

    private function buildGraphQLQuery(array $data, int $type): array
    {
        return [
            'variables' => ['data' => $data, 'type' => $type],
            'query' => '
                mutation ($data: DebtCreateInputGraphQLType!, $type: Int!) {
                    createDebtScoring(data: $data, type: $type)
                }
            ',
        ];
    }

    private function assertSuccessCreateDebtScoringResponse(TestResponse $response, int $type): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure(['data' => ['createDebtScoring']]);

        $scoringRequest = ScoringRequest::query()->find($response->json('data.createDebtScoring'));
        $this->assertNotNull($scoringRequest);
        $this->assertEquals($type, $scoringRequest->type->value);
        $this->assertEquals($this->_input['pinfl'], $scoringRequest->request->pinfl);
        if ($type === $this->mibType) {
            $this->assertEquals('+' . $this->_phone, $scoringRequest->request->phone);
        }
    }

    private function assertValidationErrors(TestResponse $response): void
    {
        $this->assertEquals(
            trans('validation.digits', ['attribute' => 'data.pinfl', 'digits' => 14]),
            $response->json('errors.0.extensions.validation')['data.pinfl'][0]
        );
        $this->assertEquals(
            trans('validation.digits', ['attribute' => 'data.phone', 'digits' => 12]),
            $response->json('errors.0.extensions.validation')['data.phone'][0]
        );
    }
}
