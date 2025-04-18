<?php

namespace App\Services;

use App\Clients\IcanClient;
use App\DTOs\Ican\IcanCreditDTO;
use App\DTOs\Ican\IcanMetaDTO;
use App\DTOs\Ican\IcanTransactionDTO;
use App\DTOs\Ican\IcanUserDocumentDataDTO;
use App\Enums\Company\CompanySettingsEnum;
use App\Enums\Company\CustomerTypeEnum;
use App\Enums\Company\InstallmentStatusEnum;
use App\Enums\Company\InstallmentTypeEnum;
use App\Enums\Company\TransactionStatusEnum;
use App\Enums\External\IcanTransactionStatusEnum;
use App\Models\Company\CompanyAdmin;
use App\Models\Company\CompanySettings;
use App\Models\Company\Customer;
use App\Models\Company\Installment;
use App\Models\Company\Tariff;
use App\Models\Company\Transaction;
use App\Models\Company\TransactionType;

class IcanService
{
    public function __construct(private readonly IcanClient $icanClient) {}

    public function syncCredits(int $page): IcanMetaDTO
    {
        $credits = $this->icanClient->getCredits($page);

        /** @var IcanCreditDTO $credit */
        foreach ($credits->data as $credit) {
            $installment = Installment::query()->where('ican_id', $credit->id)->first();

            if (! $installment) {
                $this->createOrUpdateInstallment($credit);

                continue;
            }

            if ($installment->updated_at->equalTo($credit->updated_at)) {
                $this->createOrUpdateInstallment($credit, $installment);
            }
        }

        return $credits->meta;
    }

    public function syncTransactions(int $page): IcanMetaDTO
    {
        $transactions = $this->icanClient->getTransactions($page);

        /** @var IcanTransactionDTO $transactionDto */
        foreach ($transactions->data as $transactionDto) {
            // Skip if transaction status is not SUCCESS or CANCELED
            if (! in_array($transactionDto->status, IcanTransactionStatusEnum::values())) {
                continue;
            }

            $transaction = Transaction::query()->where('ican_id', $transactionDto->id)->first();

            if (! $transaction) {
                $this->createOrUpdateTransaction($transactionDto);

                continue;
            }

            // Check if the transaction status has changed
            if (IcanTransactionStatusEnum::from($transactionDto->status)->name !== $transaction->status->name) {
                $this->createOrUpdateTransaction($transactionDto, $transaction);
            }
        }

        return $transactions->meta;
    }

    private function createOrGetTariff(IcanCreditDTO $credit)
    {
        Tariff::query()->firstOrCreate([
            'ican_id' => $credit->tariff_id,
        ])->update([
            'name' => $credit->tariff->name,
            'percent' => $credit->tariff->percent,
            'min_period' => $credit->tariff->min_period,
            'max_period' => $credit->tariff->max_period,
            'min_amount' => $credit->tariff->min_amount,
            'max_amount' => $credit->tariff->max_amount,
            'min_initial_fee' => 0,
            'max_initial_fee' => 100_000_000_000,
        ]);
    }

    private function createOrGetCustomer(IcanUserDocumentDataDTO $icanUserDocumentData)
    {
        Customer::query()->firstOrCreate([
            'pinfl' => $icanUserDocumentData->pinfl,
        ])->update([
            'tin' => $icanUserDocumentData->inn,
            'passport_series' => $icanUserDocumentData->passport_series,
            'passport_number' => $icanUserDocumentData->passport_number,
            'passport_given_date' => $icanUserDocumentData->passport_issue_date,
            'passport_expiration_date' => $icanUserDocumentData->passport_expiry_date,
            'first_name' => $icanUserDocumentData->first_name,
            'last_name' => $icanUserDocumentData->last_name,
            'middle_name' => $icanUserDocumentData->middle_name,
            'birth_date' => $icanUserDocumentData->birth_date,
            'gender' => $icanUserDocumentData->gender,
            'type' => CustomerTypeEnum::INSTALLMENT,
        ]);
    }

    private function createOrGetInstallment(IcanCreditDTO $credit)
    {
        Installment::query()->firstOrCreate([
            'ican_id' => $credit->id,
        ])->update([
            'customer_id' => $this->createOrGetCustomer($credit->person_main->user_document->data),
            'branch_id' => CompanySettings::getConfig(CompanySettingsEnum::ICAN_BRANCH_ID),
            'tariff_id' => $this->createOrGetTariff($credit),
            'admin_id' => CompanyAdmin::getIcanIntegrator()->id,
            'type' => InstallmentTypeEnum::WITHOUT_CARD,
            'amount' => $credit->amount / 100,
            'initial_fee' => $credit->initial_payment / 100,
            'percent' => $credit->percent,
            'description' => $credit->comment,
            'sms_notify' => false,
            'any_card_charge' => false,
            'is_blacklist' => false,
            'status' => InstallmentStatusEnum::ACTIVE,
            'period' => $credit->period,
            'canceled_at' => $credit->canceled_at,
            'cancellation_details' => $credit->cancel_reason,
            'starts_in' => $credit->activated_at,
            'paid_amount' => ($credit->amount - $credit->main_debt) / 100,
        ]);
    }

    private function createOrUpdateInstallment(IcanCreditDTO $credit, ?Installment $installment = null): void
    {
        $data = [
            'ican_id' => $credit->id,
            'customer_id' => $this->createOrGetCustomer($credit->person_main->user_document->data),
            'branch_id' => CompanySettings::getConfig(CompanySettingsEnum::ICAN_BRANCH_ID),
            'tariff_id' => $this->createOrGetTariff($credit),
            'admin_id' => CompanyAdmin::getIcanIntegrator()->id,
            'type' => InstallmentTypeEnum::WITHOUT_CARD,
            'amount' => $credit->amount / 100,
            'initial_fee' => $credit->initial_payment / 100,
            'percent' => $credit->percent,
            'description' => $credit->comment,
            'sms_notify' => false,
            'any_card_charge' => false,
            'is_blacklist' => false,
            'status' => InstallmentStatusEnum::ACTIVE,
            'period' => $credit->period,
            'canceled_at' => $credit->canceled_at,
            'cancellation_details' => $credit->cancel_reason,
            'starts_in' => $credit->activated_at,
            'paid_amount' => ($credit->amount - $credit->main_debt) / 100,
            'updated_at' => $credit->updated_at,
        ];

        if ($installment) {
            $installment->update($data);
        } else {
            Installment::create($data);
        }
    }

    private function createOrUpdateTransaction(IcanTransactionDTO $transactionDto, ?Transaction $transaction = null): void
    {
        $data = [
            'installment_id' => $this->createOrGetInstallment($transactionDto->credit),
            'branch_id' => CompanySettings::getConfig(CompanySettingsEnum::ICAN_BRANCH_ID),
            'customer_id' => $this->createOrGetCustomer($transactionDto->credit->person_main->user_document->data),
            'transaction_type_id' => TransactionType::ICAN_INTEGRATION,
            'amount' => $transactionDto->amount,
            'admin_id' => CompanyAdmin::getIcanIntegrator()->id,
            'paid_at' => $transactionDto->payments->first()->created_at,
            'card_pan' => $transactionDto->service_data->pan,
            'is_initial_fee' => $transactionDto->payments->first()->is_initial_payment,
            'status' => TransactionStatusEnum::SUCCESS,
        ];

        if ($transaction) {
            $transaction->update($data);
        } else {
            Transaction::create($data);
        }
    }
}
