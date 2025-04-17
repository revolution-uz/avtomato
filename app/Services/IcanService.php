<?php

namespace App\Services;

use App\Clients\IcanClient;
use App\DTOs\Ican\IcanCreditDTO;
use App\DTOs\Ican\IcanMetaDTO;
use App\DTOs\Ican\IcanTransactionDTO;
use App\Enums\Company\CompanySettingsEnum;
use App\Enums\Company\CustomerTypeEnum;
use App\Enums\Company\InstallmentStatusEnum;
use App\Enums\Company\InstallmentTypeEnum;
use App\Enums\Company\TransactionStatusEnum;
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
            Installment::query()->updateOrCreate([
                'ican_id' => $credit->id,
            ])->update([
                'customer_id' => $this->createOrGetCustomer($credit->client_id, $credit),
                'branch_id' => CompanySettings::getConfig(CompanySettingsEnum::ICAN_BRANCH_ID),
                'tariff_id' => $this->createOrGetTariff($credit->tariff_id, $credit),
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

        return $credits->meta;
    }

    public function syncTransactions(int $page): IcanMetaDTO
    {
        $transactions = $this->icanClient->getTransactions($page);

        /** @var IcanTransactionDTO $transaction */
        foreach ($transactions->data as $transaction) {
            Transaction::query()->firstOrCreate([
                'ican_id' => $transaction->id,
            ])->update([
                'installment_id' => $this->createOrGetInstallment($transaction->credit_id, $transaction->credit),
                'branch_id' => CompanySettings::getConfig(CompanySettingsEnum::ICAN_BRANCH_ID),
                'customer_id' => $this->createOrGetCustomer($transaction->credit->client_id, $transaction->credit),
                'transaction_type_id' => TransactionType::ICAN_INTEGRATION,
                'amount' => $transaction->amount,
                'admin_id' => CompanyAdmin::getIcanIntegrator()->id,
                'paid_at' => $transaction->payments->first()->created_at,
                'card_pan' => $transaction->service_data->pan,
                'is_initial_fee' => $transaction->payments->first()->is_initial_payment,
                'status' => TransactionStatusEnum::SUCCESS,
            ]);
        }

        return $transactions->meta;
    }

    private function createOrGetTariff(int $tariff_id, IcanCreditDTO $credit)
    {
        Tariff::query()->firstOrCreate([
            'ican_id' => $tariff_id,
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

    private function createOrGetCustomer(int $client_id, IcanCreditDTO $credit)
    {
        Customer::query()->firstOrCreate([
            'ican_id' => $client_id,
        ])->update([
            'pinfl' => $credit->person_main->user_document->data->pinfl,
            'tin' => $credit->person_main->user_document->data->inn,
            'passport_series' => $credit->person_main->user_document->data->passport_series,
            'passport_number' => $credit->person_main->user_document->data->passport_number,
            'passport_given_date' => $credit->person_main->user_document->data->passport_issue_date,
            'passport_expiration_date' => $credit->person_main->user_document->data->passport_expiry_date,
            'first_name' => $credit->person_main->user_document->data->first_name,
            'last_name' => $credit->person_main->user_document->data->last_name,
            'middle_name' => $credit->person_main->user_document->data->middle_name,
            'birth_date' => $credit->person_main->user_document->data->birth_date,
            'gender' => $credit->person_main->user_document->data->gender,
            'type' => CustomerTypeEnum::INSTALLMENT,
        ]);
    }

    private function createOrGetInstallment(int $credit_id, IcanCreditDTO $credit)
    {
        Installment::query()->firstOrCreate([
            'ican_id' => $credit_id,
        ])->update([
            'customer_id' => $this->createOrGetCustomer($credit->client_id, $credit),
            'branch_id' => CompanySettings::getConfig(CompanySettingsEnum::ICAN_BRANCH_ID),
            'tariff_id' => $this->createOrGetTariff($credit->tariff_id, $credit),
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
}
