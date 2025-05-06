<?php

namespace App\Services;

use App\Clients\IcanClient;
use App\DTOs\Ican\IcanCreditDTO;
use App\DTOs\Ican\IcanMetaDTO;
use App\DTOs\Ican\IcanTariffDTO;
use App\DTOs\Ican\IcanTransactionDTO;
use App\DTOs\Ican\IcanUserDocumentDataDTO;
use App\DTOs\Ican\Request\IcanTransactionRequestDTO;
use App\Enums\Company\CompanySettingsEnum;
use App\Enums\Company\CustomerTypeEnum;
use App\Enums\Company\InstallmentStatusEnum;
use App\Enums\Company\InstallmentTypeEnum;
use App\Enums\Company\TransactionStatusEnum;
use App\Enums\External\IcanCreditStatusEnum;
use App\Enums\External\IcanTransactionStatusEnum;
use App\Enums\GenderEnum;
use App\Exceptions\GeneralException;
use App\Models\Company\CompanyAdmin;
use App\Models\Company\CompanySettings;
use App\Models\Company\Customer;
use App\Models\Company\Installment;
use App\Models\Company\Tariff;
use App\Models\Company\Transaction;
use App\Models\Company\TransactionType;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Throwable;

class IcanService
{
    private string $logChannel = 'ican-service';

    public function __construct(private readonly IcanClient $icanClient) {}

    public function syncTariffs(int $page): bool
    {
        /**
         * @var Collection<IcanTariffDTO>|null $credits
         * @var IcanMetaDTO|null $meta
         */
        ['tariffs' => $tariffs, 'meta' => $meta] = $this->icanClient->getTariffs($page);

        if (! $tariffs || ! $meta) {
            return false;
        }

        Log::info($tariffs);

        foreach ($tariffs as $tariff) {
            try {
                $this->createOrUpdateTariff($tariff);
            } catch (Throwable $e) {
                Log::channel($this->logChannel)->error('Error syncing credits: ' . $e->getMessage(), ['exception' => $e]);
            }
        }

        return $meta->last_page > $page;
    }


    public function syncCredits(int $page): bool
    {
        /**
         * @var Collection<IcanCreditDTO>|null $credits
         * @var IcanMetaDTO|null $meta
         */
        ['credits' => $credits, 'meta' => $meta] = $this->icanClient->getCredits($page);

        if (! $credits || ! $meta) {
            return false;
        }

        foreach ($credits as $credit) {
            try {
                $this->createOrUpdateInstallment($credit);

            } catch (Throwable $e) {
                Log::channel($this->logChannel)->error('Error syncing credits: ' . $e->getMessage(), ['exception' => $e]);
            }
        }

        return $meta->last_page > $page;
    }

    public function syncTransactions(int $page): bool
    {
        /**
         * @var Collection<IcanTransactionDTO>|null $transactions
         * @var IcanMetaDTO|null $meta
         */
        ['transactions' => $transactions, 'meta' => $meta] = $this->icanClient->getTransactions(IcanTransactionStatusEnum::SUCCESS, $page);

        if (! $transactions || ! $meta) {
            return false;
        }

        foreach ($transactions as $transactionDto) {
            try {
                $transaction = $this->createOrUpdateTransaction($transactionDto);

                if (! $transaction->wasRecentlyCreated) {
                    return false;
                }

            } catch (Throwable $e) {
                Log::channel($this->logChannel)->error('Error syncing credits: ' . $e->getMessage(), ['exception' => $e]);
            }
        }

        return $meta->last_page > $page;
    }

    public function syncCanceledTransactions(int $page): bool
    {
        /**
         * @var Collection<IcanTransactionDTO>|null $transactions
         * @var IcanMetaDTO|null $meta
         */
        ['transactions' => $transactions, 'meta' => $meta] = $this->icanClient->getTransactions(IcanTransactionStatusEnum::CANCELED, $page);

        if (! $transactions || ! $meta) {
            return false;
        }

        foreach ($transactions as $transactionDto) {
            try {
                $this->createOrUpdateTransaction($transactionDto);

            } catch (Throwable $e) {
                Log::channel($this->logChannel)->error('Error syncing credits: ' . $e->getMessage(), ['exception' => $e]);
            }
        }

        return $meta->last_page > $page;
    }

    public function getInstallmentDebtAmount(int $ican_id): ?float
    {
        $icanCreditDto = $this->icanClient->getSingleCredit($ican_id);

        return $icanCreditDto->overdue_debt;
    }

    public function createTransaction(Transaction $transaction): ?IcanTransactionDTO
    {
        $dto = IcanTransactionRequestDTO::from([
            'credit_id' => $transaction->installment->ican_id,
            'amount' => $transaction->amount,
            'employee_id' => $this->getEmployeeId(),
        ]);

        return $this->icanClient->createTransaction($dto);
    }

    private function createOrUpdateTariff(IcanCreditDTO $credit): Tariff
    {
        return Tariff::query()->updateOrCreate([
            'ican_id' => $credit->tariff_id,
        ], [
            'name' => $credit->tariff?->name,
            'percent' => $credit->tariff?->percent,
            'min_period' => $credit->tariff?->min_period,
            'max_period' => $credit->tariff?->max_period,
            'min_amount' => $credit->tariff?->min_amount,
            'max_amount' => $credit->tariff?->max_amount,
            'min_initial_fee' => 0,
            'max_initial_fee' => 2_000_000_000,
        ]);
    }

    private function createOrGetCustomer(IcanUserDocumentDataDTO $icanUserDocumentData): Customer
    {
        return Customer::query()->firstOrCreate([
            'pinfl' => $icanUserDocumentData->pinfl,
        ], [
            'tin' => $icanUserDocumentData->inn,
            'passport_series' => $icanUserDocumentData->passport_series,
            'passport_number' => $icanUserDocumentData->passport_number,
            'passport_given_date' => $icanUserDocumentData->passport_issue_date,
            'passport_expiration_date' => $icanUserDocumentData->passport_expiry_date,
            'first_name' => $icanUserDocumentData->first_name,
            'last_name' => $icanUserDocumentData->last_name,
            'middle_name' => $icanUserDocumentData->middle_name,
            'birth_date' => $icanUserDocumentData->birth_date,
            'gender' => $icanUserDocumentData->gender === 'male' ? GenderEnum::MALE : GenderEnum::FEMALE,
            'type' => CustomerTypeEnum::INSTALLMENT,
        ]);
    }

    private function createOrUpdateInstallment(IcanCreditDTO $credit): Installment
    {
        $installment = Installment::query()->firstOrNew(['ican_id' => $credit->id]);

        $installment->fill([
            'ican_id' => $credit->id,
            'customer_id' => $this->createOrGetCustomer($credit->person_main->user_document->data)->id,
            'branch_id' => CompanySettings::getConfig(CompanySettingsEnum::ICAN_BRANCH_ID),
            'tariff_id' => $this->createOrUpdateTariff($credit)->id,
            'admin_id' => CompanyAdmin::getIcanIntegrator()->id,
            'type' => InstallmentTypeEnum::WITHOUT_CARD,
            'amount' => $credit->amount,
            'initial_fee' => $credit->initial_payment,
            'percent' => $credit->percent,
            'description' => $credit->comment,
            'sms_notify' => false,
            'any_card_charge' => false,
            'is_blacklist' => false,
            'status' => match ($credit->status) {
                IcanCreditStatusEnum::CANCELED->value => InstallmentStatusEnum::CANCELED,
                IcanCreditStatusEnum::ACTIVE->value => InstallmentStatusEnum::ACTIVE,
                IcanCreditStatusEnum::WAITING_FOR_CONFIRMATION->value => InstallmentStatusEnum::INACTIVE,
                IcanCreditStatusEnum::WAITING_FOR_INITIAL_PAYMENT->value => InstallmentStatusEnum::INACTIVE,
                IcanCreditStatusEnum::COMPLETED->value => InstallmentStatusEnum::CLOSED,
                default => new GeneralException('Invalid credit status'),
            },
            'period' => $credit->period,
            'canceled_at' => $credit->canceled_at,
            'cancellation_details' => $credit->cancel_reason,
            'starts_in' => $credit->activated_at ?: $credit->created_at,
            'paid_amount' => $credit->amount - $credit->main_debt,
            'updated_at' => $credit->updated_at,
        ]);

        if (! $installment->exists || $installment->isDirty()) {
            $installment->saveQuietly();
        }

        return $installment;
    }

    private function createOrUpdateTransaction(IcanTransactionDTO $transactionDto): Transaction
    {
        $transaction = Transaction::query()->firstOrNew(['ican_id' => $transactionDto->id]);

        $transaction->fill([
            'ican_id' => $transactionDto->id,
            'installment_id' => $this->createOrUpdateInstallment($transactionDto->credit)->id,
            'branch_id' => CompanySettings::getConfig(CompanySettingsEnum::ICAN_BRANCH_ID),
            'customer_id' => $this->createOrGetCustomer($transactionDto->credit->person_main->user_document->data)->id,
            'transaction_type_id' => TransactionType::ICAN_INTEGRATION,
            'amount' => $transactionDto->amount,
            'admin_id' => CompanyAdmin::getIcanIntegrator()->id,
            'paid_at' => $transactionDto->payments->first()?->created_at,
            'card_pan' => $transactionDto->service_data?->pan,
            'is_initial_fee' => $transactionDto->payments->first()?->is_initial_payment,
            'status' => match ($transactionDto->status) {
                IcanTransactionStatusEnum::SUCCESS->value => TransactionStatusEnum::SUCCESS,
                IcanTransactionStatusEnum::CANCELED->value => TransactionStatusEnum::CANCELED,
                default => new GeneralException('Invalid transaction status'),
            },
        ]);

        if (! $transaction->exists || $transaction->isDirty()) {
            $transaction->saveQuietly();
        }

        return $transaction;
    }

    private function getEmployeeId()
    {
        $employeeId = CompanySettings::getConfig(CompanySettingsEnum::ICAN_EMPLOYEE_ID);

        if (empty($employeeId)) {
            $icanProfileDto = $this->icanClient->getProfile();

            if (! $icanProfileDto) {
                throw new GeneralException('Could not get Ican profile');
            }

            $employeeId = $icanProfileDto->id;

            CompanySettings::query()
                ->where('name', CompanySettingsEnum::ICAN_EMPLOYEE_ID)
                ->update(['value' => $employeeId]);
        }

        return $employeeId;
    }
}
