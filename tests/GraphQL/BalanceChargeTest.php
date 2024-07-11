<?php

namespace Tests\GraphQL;

use App\Enums\Company\Scoring\ScoringRequestStatusEnum;
use App\Enums\FeatureEnum;
use App\Models\Company\BalanceCharge;
use App\Models\Company\BalanceFund;
use App\Models\Company\Feature;
use App\Models\Company\Installment;
use App\Models\Company\ScoringRequest;
use App\Models\Company\Transaction;
use App\Services\User\BalanceService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use PHPUnit\Framework\Attributes\Test;

class BalanceChargeTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private Builder|Model $_feature;
    private ScoringRequest $_scoringRequest;
    private Transaction $_transaction;

    protected function setUp(): void
    {
        parent::setUp();

        Installment::query()->forceDelete();
        Transaction::query()->forceDelete();
        ScoringRequest::query()->forceDelete();
        BalanceFund::query()->forceDelete();
        $this->_scoringRequest = ScoringRequest::factory()->create();
        $this->_transaction = Transaction::factory()->create();
        $this->_feature = Feature::query()->updateOrCreate(
            [
                'name' => FeatureEnum::BALANCE_CHARGE,
            ],
            [
                'amount' => 1000,
            ]
        );

        BalanceFund::factory()->create(
            [
                'amount' => 10000,
            ]
        );
        BalanceCharge::query()->create(
            [
                'amount' => $this->_feature->chargeable_amount,
                'chargeable_id' => $this->_scoringRequest->id,
                'chargeable_type' => get_class($this->_scoringRequest),
                'feature_id' => $this->_feature->id,
            ]
        );
    }

    #[Test]
    public function balanceChargeWithScoring()
    {
        $initialBalance = BalanceFund::query()->sum('amount') - BalanceCharge::query()->sum('amount');
        $balanceService = new BalanceService;

        /* @var Feature $feature */
        $feature = $this->_feature;

        $balanceService->chargeAmount(FeatureEnum::BALANCE_CHARGE, $this->_scoringRequest);
        $this->assertDatabaseHas('balance_charges', [
            'amount' => $feature->chargeable_amount,
            'feature_id' => $feature->id,
            'chargeable_id' => $this->_scoringRequest->id,
            'chargeable_type' => ScoringRequest::class,
        ]);

        $expectedBalance = $initialBalance - $feature->chargeable_amount;
        $currentBalance = $balanceService->getBalance();
        $this->assertEquals($expectedBalance, $currentBalance, 'The balance after charging should be correctly updated.');
    }

    #[Test]
    public function balanceChargeWithTransaction()
    {
        /* @var Feature $feature */
        $feature = $this->_feature;
        $initialBalance = BalanceFund::query()->sum('amount') - BalanceCharge::query()->sum('amount');
        $balanceService = new BalanceService;
        $balanceService->chargeAmount(FeatureEnum::BALANCE_CHARGE, $this->_transaction);
        $this->assertDatabaseHas('balance_charges', [
            'amount' => $feature->chargeable_amount,
            'feature_id' => $feature->id,
            'chargeable_id' => $this->_transaction->id,
            'chargeable_type' => Transaction::class,
        ]);
        $expectedBalance = $initialBalance - $feature->chargeable_amount;
        $currentBalance = $balanceService->getBalance();
        $this->assertEquals($expectedBalance, $currentBalance, 'The balance after charging should be correctly updated.');
    }

    #[Test]
    public function isDeletedBalanceCharge()
    {
        $balanceCharge = $this->getBalanceChargeByScoringRequestId($this->_scoringRequest->id);
        $this->assertNotNull($balanceCharge);
        $this->_scoringRequest->update([
            'status' => ScoringRequestStatusEnum::FAILED,
        ]);
        $balanceCharge = $this->getBalanceChargeByScoringRequestId($this->_scoringRequest->id);
        $this->assertNull($balanceCharge);
    }

    private function getBalanceChargeByScoringRequestId($scoringRequestId): Model|Builder|null
    {
        return BalanceCharge::query()->where('chargeable_id', $scoringRequestId)->first();
    }
}
