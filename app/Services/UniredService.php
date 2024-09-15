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
use App\DTOs\Unired\UniredResponseDTO;
use App\Enums\FeatureEnum;
use App\Helpers\Utilities;
use App\Models\Company\Customer;
use App\Models\Company\Feature;
use App\Models\Company\Transaction;

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
}
