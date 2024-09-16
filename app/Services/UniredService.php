<?php

namespace App\Services;

use App\Clients\UniredClient;
use App\DTOs\Unired\Branch\UniredCreateBranchRequestDTO;
use App\DTOs\Unired\Branch\UniredToggleBranchAutoRequestDTO;
use App\DTOs\Unired\Branch\UniredUpdateBranchRequestDTO;
use App\DTOs\Unired\Client\UniredClientExtIdDTO;
use App\DTOs\Unired\Client\UniredCreateOrUpdateClientRequestDTO;
use App\DTOs\Unired\Client\UniredToggleClientAutoRequestDTO;
use App\DTOs\Unired\Payment\UniredCancelPaymentRequestDTO;
use App\DTOs\Unired\System\UniredSetWebhookDTO;
use App\DTOs\Unired\UniredResponseDTO;
use App\Enums\FeatureEnum;
use App\Helpers\Utilities;
use App\Http\Requests\UniredPaymentWebhookCallbackRequest;
use App\Models\Company\Customer;
use App\Models\Company\Feature;
use App\Models\Company\Transaction;
use App\Models\Company\TransactionType;
use Carbon\Carbon;

class UniredService
{
    public function __construct(private UniredClient $uniredClient) {}

    public function createCompany(): bool
    {
        $feature = Feature::findByName(FeatureEnum::UNIRED_PAY);
        $uniredResponseDto = $this->uniredClient->createBranch(UniredCreateBranchRequestDTO::from($feature));
        $company = UniredUpdateBranchRequestDTO::from($uniredResponseDto->result);
        $feature['id'] = $company->id;

        return $feature->saveOrFail();
    }

    public function updateCompany(): void
    {
        $this->uniredClient->updateBranch(UniredUpdateBranchRequestDTO::from(Feature::findByName(FeatureEnum::UNIRED_PAY)));
    }

    public function switchOffCompany(): void
    {
        $feature = Feature::findByName(FeatureEnum::UNIRED_PAY);
        $this->uniredClient->toggleBranchAuto(new UniredToggleBranchAutoRequestDTO($feature->id, false));
    }

    public function switchOnCompany(): void
    {
        $feature = Feature::findByName(FeatureEnum::UNIRED_PAY);
        $this->uniredClient->toggleBranchAuto(new UniredToggleBranchAutoRequestDTO($feature->id, true));
    }

    public function createOrUpdateCustomer(Customer $customer)
    {
        $this->uniredClient->createOrUpdateClients(new UniredCreateOrUpdateClientRequestDTO($customer->pinfl, $customer->passport_series . $customer->passport_number, Utilities::generateIdByTenancy($customer->id), Utilities::generateIdByTenancy($customer->id), $customer->first_name, $customer->last_name, $customer->middle_name, tenant('id'), $customer->debtAmount(), $customer->onlyPhones()));
    }

    public function switchOffCustomer(Customer $customer): void
    {
        $this->uniredClient->toggleClientAuto(new UniredToggleClientAutoRequestDTO(Utilities::generateIdByTenancy($customer->id), false));
    }

    public function switchOnCustomer(Customer $customer): void
    {
        $this->uniredClient->toggleClientAuto(new UniredToggleClientAutoRequestDTO(Utilities::generateIdByTenancy($customer->id), true));
    }

    public function deleteCustomer(Customer $customer)
    {
        return $this->uniredClient->deleteClient(new UniredClientExtIdDTO(Utilities::generateIdByTenancy($customer->id)));
    }

    public function reversePayment(Transaction $transaction): UniredResponseDTO
    {
        return $this->uniredClient->cancelPayment(new UniredCancelPaymentRequestDTO(Utilities::generateIdByTenancy($transaction->id), $transaction->external_id));
    }

    public function setWebhook()
    {
        return $this->uniredClient->setWebhook(new UniredSetWebhookDTO('http://automato.uz/api/unired-callback/web-hook'));
    }

    public function getClientDebt(?int $customer_id): ?float
    {
        if ($customer = Customer::find($customer_id)) {
            return $customer->debtAmount();
        }

        return null;
    }

    public function sendPaymentSms(mixed $getPhone, ?string $getCardPan): bool
    {
        return true;
    }

    public function createTransaction(Customer $customer, UniredPaymentWebhookCallbackRequest $request): bool
    {
        $amount = $request->amount / 100;
        foreach ($customer->installments as $installment) {
            $current_amount = min($amount, $installment->debtAmount);
            if (! $current_amount) {
                continue;
            }
            $transaction = new Transaction;
            $transaction->transaction_type_id = TransactionType::UNIRED_WITH_PINFL;
            $transaction->external_id = $request->ext_id;
            $transaction->amount = $current_amount;
            $transaction->installment_id = $installment->id;
            $transaction->customer_id = $customer->id;
            $transaction->card_pan = $request->card_mask;
            $transaction->paid_at = Carbon::createFromTimestampMs($request->created_at);
            if ($transaction->save()) {
                $amount -= $current_amount;
            }
        }

        return true;
    }
}
