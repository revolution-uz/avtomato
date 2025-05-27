<?php

namespace App\Services;

use App\Clients\UniredPinflClient;
use App\DTOs\Unired\Branch\UniredBranchResponseDTO;
use App\DTOs\Unired\Client\UniredAddClientPhonesRequestDTO;
use App\DTOs\Unired\Client\UniredClientPinflCardUpdateRequestDTO;
use App\DTOs\Unired\Client\UniredClientPinflGetCardRequestDTO;
use App\DTOs\Unired\Client\UniredClientPinflGetCardUpdatesRequestDTO;
use App\DTOs\Unired\Client\UniredContractLoanIdDTO;
use App\DTOs\Unired\Client\UniredCreateClientRequestDTO;
use App\DTOs\Unired\Client\UniredPinflCardsResponseDTO;
use App\DTOs\Unired\Client\UniredPinflCardUpdateResponseDTO;
use App\DTOs\Unired\Client\UniredToggleClientAutoRequestDTO;
use App\DTOs\Unired\Contract\UniredCreateContractRequestDTO;
use App\DTOs\Unired\Contract\UniredUpdateContractDataRequestDTO;
use App\DTOs\Unired\Contract\UniredUpdateContractRequestDTO;
use App\DTOs\Unired\Payment\UniredCancelPaymentRequestDTO;
use App\DTOs\Unired\Payment\UniredGetPaymentsPaginationRequestDTO;
use App\DTOs\Unired\Payment\UniredGetPaymentsPaginationResponseDTO;
use App\DTOs\Unired\System\UniredSetWebhookDTO;
use App\DTOs\Unired\UniredResponseDTO;
use App\Enums\Company\CardTypeEnum;
use App\Enums\Company\TerminalTypeEnum;
use App\Enums\FeatureEnum;
use App\Exceptions\GeneralException;
use App\Helpers\Utilities;
use App\Models\Company\BalanceCharge;
use App\Models\Company\Customer;
use App\Models\Company\Feature;
use App\Models\Company\Installment;
use App\Models\Company\InstallmentGuarantor;
use App\Models\Company\Transaction;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class UniredService
{
    public function __construct(private UniredPinflClient $uniredClient) {}

    public function createCustomer(Customer $customer): ?bool
    {
        $response = $this->uniredClient->createClients(new UniredCreateClientRequestDTO(
            $customer->pinfl,
            $customer->passport_series . $customer->passport_number,
            $customer->first_name,
            $customer->last_name,
            $customer->middle_name,
        ));
        // @phpstan-ignore-next-line
        if ($response->status && $customer->phones && $customer->phones->isNotEmpty()) {
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
        $feature = Feature::findByName(FeatureEnum::UNIRED_PINFL_PAY);
        $branchFeatureDto = UniredBranchResponseDTO::from($feature->getTerminalData(TerminalTypeEnum::UNIRED_PINFL, $installment->branch_id) ?? $feature->original_data);
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
        $feature = Feature::findByName(FeatureEnum::UNIRED_PINFL_PAY);
        $branchFeatureDto = UniredBranchResponseDTO::from($feature->getTerminalData(TerminalTypeEnum::UNIRED_PINFL, $installmentGuarantor->installment->branch_id) ?? $feature->original_data);

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

    public function getAndSyncPinflCards(Customer $customer): bool
    {
        $centralCustomer = $customer->getOrCreateCentralCustomer();

        $response = $this->uniredClient->getCards(new UniredClientPinflGetCardRequestDTO($centralCustomer->pinfl));

        if ($response->isSuccess()) {
            $cardDto = UniredPinflCardsResponseDTO::from($response->result);

            $cards = [];
            if ($cardDto->uzcard && $cardDto->uzcard->data) {
                $cards = array_merge($cards, array_map(fn ($card) => $card + ['type' => CardTypeEnum::UZCARD->value], $cardDto->uzcard->data->toArray()));
            }
            if ($cardDto->humo && $cardDto->humo->data) {
                $cards = array_merge($cards, array_map(fn ($card) => $card + ['type' => CardTypeEnum::HUMO->value], $cardDto->humo->data->toArray()));
            }

            $centralCustomer->uniredCards()->upsert(
                $cards,
                ['uuid'],
                ['pinfl', 'auto', 'is_verified', 'is_blocked', 'block_reason', 'blocked_at', 'owner', 'pan', 'phone', 'type']
            );

            return true;
        }

        return false;
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

    public function reversePayment(string $external_id): UniredResponseDTO
    {
        return $this->uniredClient->cancelPayment(new UniredCancelPaymentRequestDTO($external_id));
    }

    public function setWebhook(string $webhook, bool $status)
    {
        return $this->uniredClient->setWebhook(new UniredSetWebhookDTO($webhook, config('clients.unired.pinfl.callback-token'), $status));
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

    public function updatePinflCardsList(Customer $customer): bool
    {
        $centralCustomer = $customer->getOrCreateCentralCustomer();

        // $response = $this->uniredClient->getPinflCardCounts($centralCustomer->pinfl);
        // sleep(1);

        try {
            $updatesPerformed = false;

            // Update Uzcard
            if (Utilities::isAllowedUpdatePinflCards($centralCustomer->unired_uzcard_searched_at)) {
                $response = $this->uniredClient->updateUzcardCards(new UniredClientPinflCardUpdateRequestDTO([$centralCustomer->pinfl]));

                if (
                    isset($response->result[$centralCustomer->pinfl]['cards_count']) && $response->result[$centralCustomer->pinfl]['cards_count'] > 0
                ) {
                    $centralCustomer->unired_uzcard_searched_at = Carbon::now();
                    $updatesPerformed = true;
                }

                sleep(1);
            }

            // Update Humo
            if (Utilities::isAllowedUpdatePinflCards($centralCustomer->unired_humo_searched_at)) {
                $response = $this->uniredClient->updateHumoCards(new UniredClientPinflCardUpdateRequestDTO([$centralCustomer->pinfl]));

                if (
                    isset($response->result[$centralCustomer->pinfl]['cards_count']) && $response->result[$centralCustomer->pinfl]['cards_count'] > 0
                ) {
                    $centralCustomer->unired_humo_searched_at = Carbon::now();
                    $updatesPerformed = true;
                }

                sleep(1);
            }

            if ($updatesPerformed) {
                return $centralCustomer->saveOrFail();
            }

            return false;

        } catch (Exception $e) {
            Log::channel('unired-client')->error(
                'Error updating PINFL cards list',
                [
                    'class' => get_called_class(),
                    'error' => $e->getMessage(),
                    'pinfl' => $centralCustomer->pinfl,
                ]
            );
            throw new GeneralException("Failed to update cards list for PINFL: {$centralCustomer->pinfl}");
        }
    }

    public function manualUpdatePinflCardsList(Customer $customer): bool
    {
        $centralCustomer = $customer->getOrCreateCentralCustomer();

        if (
            $customer->installments()->whereNotNull('unired_id')->doesntExist() &&
            $customer->guaranteedInstallments()->whereNotNull('installment_guarantors.unired_id')->doesntExist()
        ) {
            throw ValidationException::withMessages(['customer_id' => __('Customer does not have any installments or guaranteed installments')]);
        }

        $is_uzcard_update = Utilities::isAllowedManualUpdatePinflCards($centralCustomer->unired_uzcard_searched_at);
        $is_humo_update = Utilities::isAllowedManualUpdatePinflCards($centralCustomer->unired_humo_searched_at);

        if (! $is_uzcard_update && ! $is_humo_update) {
            throw ValidationException::withMessages(['customer_id' => __('Customer cards can only be renewed once within 24 hours')]);
        }

        $updatesPerformed = false;

        if ($is_uzcard_update) {
            $response = $this->uniredClient->updateUzcardCards(new UniredClientPinflCardUpdateRequestDTO([$centralCustomer->pinfl]));

            if (isset($response->result[$centralCustomer->pinfl]['cards_count']) && $response->result[$centralCustomer->pinfl]['cards_count'] > 0) {
                $centralCustomer->unired_uzcard_searched_at = Carbon::now();
                $updatesPerformed = true;
            }
        }

        if ($is_humo_update) {
            $response = $this->uniredClient->updateHumoCards(new UniredClientPinflCardUpdateRequestDTO([$centralCustomer->pinfl]));

            if (isset($response->result[$centralCustomer->pinfl]['cards_count']) && $response->result[$centralCustomer->pinfl]['cards_count'] > 0) {
                $centralCustomer->unired_humo_searched_at = Carbon::now();
                $updatesPerformed = true;
            }
        }

        if ($updatesPerformed) {
            return $centralCustomer->saveOrFail();
        }

        return false;
    }

    public function getCardUpdates(string $pinfl, int $page = 1): array
    {
        $response = $this->uniredClient->getCardUpdates(
            new UniredClientPinflGetCardUpdatesRequestDTO(
                date_from: Carbon::today()->subYear()->startOfMonth()->toDateString(),
                date_to: Carbon::today()->addDay()->endOfMonth()->toDateString(),
                pinfls: [$pinfl],
            ),
            $page
        );
        sleep(1);
        if ($response->isSuccess()) {
            return UniredPinflCardUpdateResponseDTO::collect(
                $response->result['last_page'] === $page ? $response->result['data'] : array_merge($response->result['data'], $this->getCardUpdates($pinfl, $page + 1))
            );
        }

        return [];
    }

    public function createTransactionFee(Transaction $transaction)
    {
        if (BalanceCharge::query()->where(['transaction_id' => $transaction->id])->exists()) {
            return;
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
