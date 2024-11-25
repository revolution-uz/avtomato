<?php

namespace App\Services;

use App\Clients\UniredClient;
use App\DTOs\Unired\Branch\UniredBranchResponseDTO;
use App\DTOs\Unired\Branch\UniredCreateBranchRequestDTO;
use App\DTOs\Unired\Branch\UniredToggleBranchAutoRequestDTO;
use App\DTOs\Unired\Branch\UniredUpdateBranchRequestDTO;
use App\DTOs\Unired\Client\UniredClientExtIdDTO;
use App\DTOs\Unired\Client\UniredCreateClientResponseDTO;
use App\DTOs\Unired\Client\UniredCreateClientResponseErrorDataDTO;
use App\DTOs\Unired\Client\UniredCreateOrUpdateClientRequestDTO;
use App\DTOs\Unired\Client\UniredToggleClientAutoRequestDTO;
use App\DTOs\Unired\Payment\UniredCancelPaymentRequestDTO;
use App\DTOs\Unired\Payment\UniredGetPaymentsPaginationRequestDTO;
use App\DTOs\Unired\Payment\UniredGetPaymentsPaginationResponseDTO;
use App\DTOs\Unired\System\UniredSetWebhookDTO;
use App\DTOs\Unired\UniredResponseDTO;
use App\Enums\FeatureEnum;
use App\Helpers\Utilities;
use App\Http\Requests\UniredPaymentWebhookCallbackRequest;
use App\Models\Company\Feature;
use App\Models\Company\Installment;
use App\Models\Company\InstallmentGuarantor;
use App\Models\Company\Transaction;
use App\Models\Company\TransactionType;
use Carbon\Carbon;

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

    public function createInstallment(Installment $installment): bool
    {
        $feature = Feature::findByName(FeatureEnum::UNIRED_PAY);
        $branchFeatureDto = UniredBranchResponseDTO::from($feature->data ?? $feature->original_data);

        $createClientResponse = $this->uniredClient->createClient(new UniredCreateOrUpdateClientRequestDTO(
            $installment->customer->pinfl,
            $installment->customer->passport_series . $installment->customer->passport_number,
            Utilities::generateIdByTenancy($installment->id),
            Utilities::generateIdByTenancy($installment->id),
            $installment->customer->first_name,
            $installment->customer->last_name,
            $installment->customer->middle_name,
            $branchFeatureDto->id,
            (int) ($installment->debtAmount * 100),
            $installment->customer->phones->pluck('phone')->toArray()
        ));
        if ($createClientResponse->status) {
            $uniredCreatedClient = UniredCreateClientResponseDTO::from($createClientResponse->result);
            $installment->update(['unired_id' => $uniredCreatedClient->id]);
        } else {
            if (isset($createClientResponse->error->data) && is_array($createClientResponse->error->data)) {
                $errorData = UniredCreateClientResponseErrorDataDTO::from($createClientResponse->error->data);
                if (isset($errorData->loan_id)) {
                    $installment->update(['unired_id' => -1]);
                }
            }
        }

        return true;
    }

    public function createOrUpdateInstallment(Installment $installment): bool
    {
        $feature = Feature::findByName(FeatureEnum::UNIRED_PAY);
        $branchFeatureDto = UniredBranchResponseDTO::from($feature->data ?? $feature->original_data);

        $this->uniredClient->createOrUpdateClients(new UniredCreateOrUpdateClientRequestDTO(
            $installment->customer->pinfl,
            $installment->customer->passport_series . $installment->customer->passport_number,
            Utilities::generateIdByTenancy($installment->id),
            Utilities::generateIdByTenancy($installment->id),
            $installment->customer->first_name,
            $installment->customer->last_name,
            $installment->customer->middle_name,
            $branchFeatureDto->id,
            (int) ($installment->debtAmount * 100),
            $installment->customer->phones->pluck('phone')->toArray()
        ));

        return true;
    }

    public function createInstallmentGuarantor(InstallmentGuarantor $installmentGuarantor): bool
    {
        $feature = Feature::findByName(FeatureEnum::UNIRED_PAY);
        $branchFeatureDto = UniredBranchResponseDTO::from($feature->data ?? $feature->original_data);

        $createClientResponse = $this->uniredClient->createClient(new UniredCreateOrUpdateClientRequestDTO(
            $installmentGuarantor->customer->pinfl,
            $installmentGuarantor->customer->passport_series . $installmentGuarantor->customer->passport_number,
            Utilities::generateIdByTenancy($installmentGuarantor->installment_id . Utilities::TENANT_SEPARATOR . $installmentGuarantor->customer_id),
            Utilities::generateIdByTenancy($installmentGuarantor->installment_id . Utilities::TENANT_SEPARATOR . $installmentGuarantor->customer_id),
            $installmentGuarantor->customer->first_name,
            $installmentGuarantor->customer->last_name,
            $installmentGuarantor->customer->middle_name,
            $branchFeatureDto->id,
            (int) ($installmentGuarantor->installment->debtAmount * 100),
            $installmentGuarantor->customer->phones->pluck('phone')->toArray()
        ));
        if ($createClientResponse->status) {
            $uniredCreatedClient = UniredCreateClientResponseDTO::from($createClientResponse->result);
            $installmentGuarantor->update(['unired_id' => $uniredCreatedClient->id]);
        } else {
            if (isset($createClientResponse->error->data) && is_array($createClientResponse->error->data)) {
                $errorData = UniredCreateClientResponseErrorDataDTO::from($createClientResponse->error->data);
                if (isset($errorData->loan_id)) {
                    $installmentGuarantor->update(['unired_id' => -1]);
                }
            }
        }

        return true;
    }

    public function createOrUpdateInstallmentGuarantor(InstallmentGuarantor $installmentGuarantor): bool
    {
        $feature = Feature::findByName(FeatureEnum::UNIRED_PAY);
        $branchFeatureDto = UniredBranchResponseDTO::from($feature->data ?? $feature->original_data);

        $this->uniredClient->createOrUpdateClients(new UniredCreateOrUpdateClientRequestDTO(
            $installmentGuarantor->customer->pinfl,
            $installmentGuarantor->customer->passport_series . $installmentGuarantor->customer->passport_number,
            Utilities::generateIdByTenancy($installmentGuarantor->installment_id . Utilities::TENANT_SEPARATOR . $installmentGuarantor->customer_id),
            Utilities::generateIdByTenancy($installmentGuarantor->installment_id . Utilities::TENANT_SEPARATOR . $installmentGuarantor->customer_id),
            $installmentGuarantor->customer->first_name,
            $installmentGuarantor->customer->last_name,
            $installmentGuarantor->customer->middle_name,
            $branchFeatureDto->id,
            (int) ($installmentGuarantor->installment->debtAmount * 100),
            $installmentGuarantor->customer->phones->pluck('phone')->toArray()
        ));

        return true;
    }

    public function switchOffInstallment(Installment $installment): void
    {
        $this->uniredClient->toggleClientAuto(new UniredToggleClientAutoRequestDTO(Utilities::generateIdByTenancy($installment->id), false));
    }

    public function switchOnInstallment(Installment $installment): void
    {
        $this->uniredClient->toggleClientAuto(new UniredToggleClientAutoRequestDTO(Utilities::generateIdByTenancy($installment->id), true));
    }

    public function deleteInstallment(Installment $installment)
    {
        return $this->uniredClient->deleteClient(new UniredClientExtIdDTO(Utilities::generateIdByTenancy($installment->id)));
    }

    public function switchOffInstallmentGuarantor(InstallmentGuarantor $installmentGuarantor): void
    {
        $this->uniredClient->toggleClientAuto(new UniredToggleClientAutoRequestDTO(Utilities::generateIdByTenancy($installmentGuarantor->id . Utilities::TENANT_SEPARATOR . $installmentGuarantor->customer_id), false));
    }

    public function switchOnInstallmentGuarantor(InstallmentGuarantor $installmentGuarantor): void
    {
        $this->uniredClient->toggleClientAuto(new UniredToggleClientAutoRequestDTO(Utilities::generateIdByTenancy($installmentGuarantor->id . Utilities::TENANT_SEPARATOR . $installmentGuarantor->customer_id), true));
    }

    public function deleteInstallmentGuarantor(InstallmentGuarantor $installmentGuarantor)
    {
        return $this->uniredClient->deleteClient(new UniredClientExtIdDTO(Utilities::generateIdByTenancy($installmentGuarantor->id . Utilities::TENANT_SEPARATOR . $installmentGuarantor->customer_id)));
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

    public function setWebhook()
    {
        return $this->uniredClient->setWebhook(new UniredSetWebhookDTO('http://automato.uz/api/unired-callback/web-hook'));
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
        $transaction = new Transaction;
        $transaction->transaction_type_id = TransactionType::UNIRED_WITH_PINFL;
        $transaction->branch_id = $installment->branch_id;
        $transaction->external_id = $request->ext_id;
        $transaction->amount = $request->amount / 100;
        $transaction->installment_id = $installment->id;
        $transaction->customer_id = $customer_id;
        $transaction->card_pan = $request->card_mask;
        $transaction->paid_at = Carbon::parse($request->created_at);

        return $transaction->save();
    }
}
