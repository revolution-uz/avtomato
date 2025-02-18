<?php

namespace App\Services;

use App\Clients\UniredClient;
use App\DTOs\Unired\Branch\UniredBranchResponseDTO;
use App\DTOs\Unired\Branch\UniredCreateBranchRequestDTO;
use App\DTOs\Unired\Branch\UniredToggleBranchAutoRequestDTO;
use App\DTOs\Unired\Branch\UniredUpdateBranchRequestDTO;
use App\DTOs\Unired\Client\UniredAddClientPhonesRequestDTO;
use App\DTOs\Unired\Client\UniredClientPinflCardUpdateRequestDTO;
use App\DTOs\Unired\Client\UniredClientPinflGetCardRequestDTO;
use App\DTOs\Unired\Client\UniredContractLoanIdDTO;
use App\DTOs\Unired\Client\UniredCreateClientRequestDTO;
use App\DTOs\Unired\Client\UniredPinflCardsResponseDTO;
use App\DTOs\Unired\Client\UniredToggleClientAutoRequestDTO;
use App\DTOs\Unired\Contract\UniredCreateContractRequestDTO;
use App\DTOs\Unired\Contract\UniredUpdateContractDataRequestDTO;
use App\DTOs\Unired\Contract\UniredUpdateContractRequestDTO;
use App\DTOs\Unired\Payment\UniredCancelPaymentRequestDTO;
use App\DTOs\Unired\Payment\UniredGetPaymentsPaginationRequestDTO;
use App\DTOs\Unired\Payment\UniredGetPaymentsPaginationResponseDTO;
use App\DTOs\Unired\System\UniredSetWebhookDTO;
use App\DTOs\Unired\UniredResponseDTO;
use App\Enums\FeatureEnum;
use App\Exceptions\GeneralException;
use App\Helpers\Utilities;
use App\Http\Requests\UniredPaymentWebhookCallbackRequest;
use App\Models\CentralCustomer;
use App\Models\Company\Customer;
use App\Models\Company\Feature;
use App\Models\Company\Installment;
use App\Models\Company\InstallmentGuarantor;
use App\Models\Company\Transaction;
use App\Models\Company\TransactionType;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;

class UniredService
{
    public function __construct(private UniredClient $uniredClient) {}

    public function createBranch(UniredCreateBranchRequestDTO $params): bool
    {
        $uniredResponseDto = $this->uniredClient->createBranch($params);
        $feature = Feature::findByName(FeatureEnum::UNIRED_PAY);
        $feature->data = UniredBranchResponseDTO::from($uniredResponseDto->result)->toArray();

        return $feature->saveOrFail();
    }

    public function updateBranch(UniredUpdateBranchRequestDTO $params): bool
    {
        $uniredResponseDto = $this->uniredClient->updateBranch($params);
        $feature = Feature::findByName(FeatureEnum::UNIRED_PAY);
        $feature->data = array_merge($feature->data, UniredUpdateBranchRequestDTO::from($uniredResponseDto->result)->toArray());

        return $feature->saveOrFail();
    }

    public function switchOffBranch(): bool
    {
        $feature = Feature::findByName(FeatureEnum::UNIRED_PAY);
        $this->uniredClient->toggleBranchAuto(new UniredToggleBranchAutoRequestDTO($feature->data['id'], false));
        $feature->data = array_merge($feature->data, ['auto_payment' => false]);

        return $feature->saveOrFail();
    }

    public function switchOnBranch(): bool
    {
        $feature = Feature::findByName(FeatureEnum::UNIRED_PAY);
        $this->uniredClient->toggleBranchAuto(new UniredToggleBranchAutoRequestDTO($feature->data['id'], true));
        $feature->data = array_merge($feature->data, ['auto_payment' => true]);

        return $feature->saveOrFail();
    }

    public function createCustomer(Customer $customer): ?bool
    {
        $response = $this->uniredClient->createClients(new UniredCreateClientRequestDTO(
            $customer->pinfl,
            $customer->passport_series . $customer->passport_number,
            $customer->first_name,
            $customer->last_name,
            $customer->middle_name,
        ));
        if ($response->status) {
            $response = $this->uniredClient->addClientPhones(new UniredAddClientPhonesRequestDTO(
                $customer->pinfl,
                array_map(fn ($phone) => substr($phone, -9), $customer->phones->pluck('phone')->toArray())
            ));

            return $response->status;
        }

        return $response->status;
    }

    public function createInstallment(Installment $installment): bool
    {
        $feature = Feature::findByName(FeatureEnum::UNIRED_PAY);
        $branchFeatureDto = UniredBranchResponseDTO::from($feature->data ?? $feature->original_data);
        if ($this->uniredClient->createContracts(new UniredCreateContractRequestDTO(
            $installment->customer->pinfl,
            Utilities::generateIdByTenancy($installment->id),
            Utilities::generateIdByTenancy($installment->id),
            $branchFeatureDto->id,
            (int) ($installment->debtAmount * 100),
        ))->isSuccess()) {
            $installment->updateOrFail(['unired_id' => 1]);

            return true;
        }

        return false;
    }

    public function updateInstallment(Installment $installment): ?bool
    {
        return $this->updateDebitAndSwitchAuto(Utilities::generateIdByTenancy($installment->id), (int) $installment->debtAmount * 100, true);
    }

    public function createInstallmentGuarantor(InstallmentGuarantor $installmentGuarantor): bool
    {
        $feature = Feature::findByName(FeatureEnum::UNIRED_PAY);
        $branchFeatureDto = UniredBranchResponseDTO::from($feature->data ?? $feature->original_data);

        if ($this->uniredClient->createContracts(new UniredCreateContractRequestDTO(
            $installmentGuarantor->customer->pinfl,
            Utilities::generateIdByTenancy($installmentGuarantor->installment_id . Utilities::TENANT_SEPARATOR . $installmentGuarantor->customer_id),
            Utilities::generateIdByTenancy($installmentGuarantor->installment_id . Utilities::TENANT_SEPARATOR . $installmentGuarantor->customer_id),
            $branchFeatureDto->id,
            (int) ($installmentGuarantor->installment->debtAmount * 100),
        ))->isSuccess()) {
            $installmentGuarantor->update(['unired_id' => 1]);

            return true;
        }

        return false;
    }

    public function updateInstallmentGuarantor(InstallmentGuarantor $installmentGuarantor): bool
    {
        return $this->updateDebitAndSwitchAuto(Utilities::generateIdByTenancy($installmentGuarantor->installment_id . Utilities::TENANT_SEPARATOR . $installmentGuarantor->customer_id), (int) ($installmentGuarantor->installment->debtAmount * 100), true);
    }

    public function switchOffInstallment(Installment $installment): ?bool
    {
        return $this->updateDebitAndSwitchAuto(Utilities::generateIdByTenancy($installment->id), (int) ($installment->debtAmount * 100), false);
    }

    public function getCards(Customer $customer): array
    {
        $response = $this->uniredClient->getCards(new UniredClientPinflGetCardRequestDTO($customer->pinfl));
        if ($response->isSuccess()) {
            $cardDto = UniredPinflCardsResponseDTO::from($response->result);

            return array_merge($cardDto->uzcard->data->toArray(), $cardDto->humo->data->toArray());
        }

        return [];
    }

    public function switchOnInstallment(Installment $installment): void
    {
        $this->updateDebitAndSwitchAuto(Utilities::generateIdByTenancy($installment->id), (int) ($installment->debtAmount * 100), true);
    }

    public function deleteInstallment(Installment $installment)
    {
        return $this->uniredClient->deleteContract(new UniredContractLoanIdDTO($installment->customer->pinfl, Utilities::generateIdByTenancy($installment->id)));
    }

    public function switchOffInstallmentGuarantor(InstallmentGuarantor $installmentGuarantor): void
    {
        $this->updateDebitAndSwitchAuto(Utilities::generateIdByTenancy($installmentGuarantor->id . Utilities::TENANT_SEPARATOR . $installmentGuarantor->customer_id), $installmentGuarantor->installment->debtAmount, false);
    }

    public function switchOnInstallmentGuarantor(InstallmentGuarantor $installmentGuarantor): void
    {
        $this->uniredClient->toggleClientAuto(new UniredToggleClientAutoRequestDTO(Utilities::generateIdByTenancy($installmentGuarantor->id . Utilities::TENANT_SEPARATOR . $installmentGuarantor->customer_id), true));
    }

    public function deleteInstallmentGuarantor(InstallmentGuarantor $installmentGuarantor)
    {
        return $this->uniredClient->deleteContract(new UniredContractLoanIdDTO($installmentGuarantor->customer->pinfl, Utilities::generateIdByTenancy($installmentGuarantor->id . Utilities::TENANT_SEPARATOR . $installmentGuarantor->customer_id)));
    }

    public function getPayments(UniredGetPaymentsPaginationRequestDTO $params): UniredGetPaymentsPaginationResponseDTO
    {
        $uniredResponseDto = $this->uniredClient->getPayments($params);

        return UniredGetPaymentsPaginationResponseDTO::from($uniredResponseDto->result);
    }

    public function reversePayment(Transaction $transaction): UniredResponseDTO
    {
        return $this->uniredClient->cancelPayment(new UniredCancelPaymentRequestDTO(Utilities::generateIdByTenancy($transaction->id), $transaction->external_id));
    }

    public function setWebhook(string $webhook, bool $status)
    {
        return $this->uniredClient->setWebhook(new UniredSetWebhookDTO($webhook, config('clients.unired.callback-token'), $status));
    }

    public function checkWebhook()
    {
        return $this->uniredClient->checkWebhook();
    }

    public function getInstallmentDebt(?int $installment_id): ?int
    {
        if ($installment = Installment::find($installment_id)) {
            return (int) ($installment->debtAmount * 100);
        }

        return null;
    }

    public function sendPaymentSms(mixed $getPhone, ?string $getCardPan): bool
    {
        return true;
    }

    public function createTransaction(Installment $installment, int $customer_id, UniredPaymentWebhookCallbackRequest $request): bool
    {
        if (Transaction::query()->where('external_id', $request->ext)->exists()) {
            return false;
        }
        $transaction = new Transaction;
        $transaction->transaction_type_id = TransactionType::UNIRED_WITH_PINFL;
        $transaction->branch_id = $installment->branch_id;
        $transaction->external_id = $request->ext;
        $transaction->amount = $request->amount / 100;
        $transaction->installment_id = $installment->id;
        $transaction->customer_id = $customer_id;
        $transaction->card_pan = $request->card['pan'] ?? '****';
        $transaction->paid_at = Carbon::parse($request->created_at);

        return $transaction->save();
    }

    public function updatePinflCardsList(Customer $customer): bool
    {
        $centralCustomer = CentralCustomer::where('pinfl', $customer->pinfl)->first();
        $response = $this->uniredClient->getPinflCardCounts($customer->pinfl);
        $is_uzcard_update = ! $response->uzcard || ! Utilities::isNotAllowedAutoUpdateCards($centralCustomer->unired_uzcard_searched_at);
        $is_humo_update = ! $response->humo || ! Utilities::isNotAllowedAutoUpdateCards($centralCustomer->unired_humo_searched_at);

        if ($centralCustomer) {
            if (
                ! $is_uzcard_update && ! $is_humo_update
                &&
                Utilities::isNotAllowedRefresheCards($centralCustomer->unired_uzcard_searched_at)
                &&
                Utilities::isNotAllowedRefresheCards($centralCustomer->unired_humo_searched_at)
            ) {
                return true;
            }
        } else {
            $centralCustomer = new CentralCustomer;
            $centralCustomer->pinfl = $customer->pinfl;
            $centralCustomer->first_name = $customer->first_name;
            $centralCustomer->last_name = $customer->last_name;
            $centralCustomer->middle_name = $customer->middle_name;
        }

        try {
            if ($is_uzcard_update) {
                $updateResult = $this->uniredClient->updateUzcardCards(new UniredClientPinflCardUpdateRequestDTO([$customer->pinfl]));
                if ($updateResult->isSuccess()) {
                    $centralCustomer->unired_uzcard_searched_at = Carbon::now();
                }
            }
            if ($is_humo_update) {
                $updateResult = $this->uniredClient->updateHumoCards(new UniredClientPinflCardUpdateRequestDTO([$customer->pinfl]));
                if ($updateResult->isSuccess()) {
                    $centralCustomer->unired_humo_searched_at = Carbon::now();
                }
            }

            return $centralCustomer->save();
        } catch (Exception $e) {
            Log::channel('unired-client')->info(
                'Error updating PINFL cards list',
                [
                    'class' => get_called_class(),
                    'error' => $e->getMessage(),
                    'pinfl' => $customer->pinfl,
                ]
            );
            throw new GeneralException("Failed to update cards list for PINFL: {$customer->pinfl}");
        }
    }

    private function updateDebitAndSwitchAuto(string $loan_id, int $debit_amount, bool $auto): ?bool
    {
        $response = $this->uniredClient->updateContract(new UniredUpdateContractRequestDTO(
            $loan_id,
            new UniredUpdateContractDataRequestDTO(
                $auto,
                $loan_id,
                $debit_amount
            )
        ));

        return $response->status;
    }
}
