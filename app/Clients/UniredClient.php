<?php

namespace App\Clients;

use App\DTOs\HttpRequestLogDTO;
use App\DTOs\Unired\Branch\UniredCreateBranchRequestDTO;
use App\DTOs\Unired\Branch\UniredToggleBranchAutoRequestDTO;
use App\DTOs\Unired\Branch\UniredUpdateBranchRequestDTO;
use App\DTOs\Unired\Client\UniredAddClientPhoneRequestDTO;
use App\DTOs\Unired\Client\UniredAddClientPhonesRequestDTO;
use App\DTOs\Unired\Client\UniredClientPinflCardCountDTO;
use App\DTOs\Unired\Client\UniredClientPinflCardUpdateRequestDTO;
use App\DTOs\Unired\Client\UniredClientPinflGetCardRequestDTO;
use App\DTOs\Unired\Client\UniredContractLoanIdDTO;
use App\DTOs\Unired\Client\UniredCreateClientRequestDTO;
use App\DTOs\Unired\Client\UniredCreateOrUpdateClientRequestDTO;
use App\DTOs\Unired\Client\UniredFindClientRequestDTO;
use App\DTOs\Unired\Client\UniredFindClientsRequestDTO;
use App\DTOs\Unired\Client\UniredToggleClientAutoRequestDTO;
use App\DTOs\Unired\Client\UniredUpdateClientDebitRequestDTO;
use App\DTOs\Unired\Contract\UniredCreateContractRequestDTO;
use App\DTOs\Unired\Contract\UniredUpdateContractRequestDTO;
use App\DTOs\Unired\Payment\UniredCancelPaymentRequestDTO;
use App\DTOs\Unired\Payment\UniredChangePaymentSendStatusRequestDTO;
use App\DTOs\Unired\Payment\UniredGetPaymentsPaginationRequestDTO;
use App\DTOs\Unired\Payment\UniredRegisterPaymentRequestDTO;
use App\DTOs\Unired\System\UniredSetTokenRequestDTO;
use App\DTOs\Unired\System\UniredSetWebhookDTO;
use App\DTOs\Unired\UniredPaginationRequestDTO;
use App\DTOs\Unired\UniredResponseDTO;
use App\Enums\ClientCodeEnum;
use App\Exceptions\UniredException;
use Illuminate\Support\Facades\Http;

class UniredClient extends BaseClient
{
    // Authorization Module
    const SYSTEM_SET_TOKEN = 'system.set.token';

    // Branch Module
    const FILIAL_CREATE = 'filial.create';
    const FILIAL_UPDATE = 'filial.update';
    const FILIAL_TOGGLE_AUTO = 'filial.toggle.auto';

    // Client Module
    const CLIENT_CREATE = 'client.create';
    const CLIENT_CREATE_OR_UPDATE = 'client.createOrUpdate';
    const CLIENT_DEBIT_UPDATE = 'client.debit.update';
    const CLIENT_FIND = 'client.find';
    const CLIENT_FIND_MULTIPLE = 'client.find.multiple';
    const CLIENT_PHONE_ADD = 'client.phone.add';
    const CLIENT_ALL = 'client.all';
    const CLIENT_AUTO_TOGGLE = 'client.auto.toggle';
    const ADD_CLIENT_PHONE = 'client.phone.add';

    // Payment Module
    const PAYMENT_GET = 'payment.get';
    const PAYMENT_CHANGE_SEND_STATUS = 'payment.change.send.status';
    const PAYMENT_REGISTER = 'payment.register';
    const PAYMENT_CANCEL = 'payment.cancel';
    const CHECK_WEBHOOK = 'webhook.check';
    const SET_WEBHOOK = 'webhook.set';

    const CONTRACT_UPDATE = 'contract.update';
    const CONTRACT_CREATE = 'contract.create';
    const CONTRACT_DELETE = 'contract.delete';
    const TRANSACTION_GET = 'transaction.get';
    const CARD_COUNT = 'card.count';
    const CARD_INFO = 'card.info';
    const PROCESSING_HUMO_SEARCH = 'processing.humo.search';
    const PROCESSING_UZCARD_SEARCH = 'processing.uzcard.search';

    private string $baseUrl;
    private string $token;

    public function __construct()
    {
        $this->baseUrl = config('clients.unired.base_url');
        $this->token = config('clients.unired.token');
        $this->channel = 'unired-client';
    }

    public function getClientCode(): ClientCodeEnum
    {
        return ClientCodeEnum::UNIRED;
    }

    public function ping(): UniredResponseDTO
    {
        return $this->httpRequest([], '', '/ping');
    }

    public function setToken(UniredSetTokenRequestDTO $params): UniredResponseDTO
    {
        return $this->makeRequest(self::SYSTEM_SET_TOKEN, $params->toArray());
    }

    public function createBranch(UniredCreateBranchRequestDTO $params): UniredResponseDTO
    {
        return $this->makeRequest(self::FILIAL_CREATE, $params->toArray());
    }

    public function updateBranch(UniredUpdateBranchRequestDTO $params): UniredResponseDTO
    {
        return $this->makeRequest(self::FILIAL_UPDATE, $params->toArray());
    }

    public function toggleBranchAuto(UniredToggleBranchAutoRequestDTO $params): UniredResponseDTO
    {
        return $this->makeRequest(self::FILIAL_TOGGLE_AUTO, $params->toArray());
    }

    public function createClient(UniredCreateOrUpdateClientRequestDTO $params): UniredResponseDTO
    {
        return $this->makeRequest(self::CLIENT_CREATE, $params->toArray());
    }

    public function createOrUpdateClients(UniredCreateOrUpdateClientRequestDTO $params): UniredResponseDTO
    {
        return $this->makeRequest(self::CLIENT_CREATE_OR_UPDATE, [$params->toArray()]);
    }

    public function updateClientDebit(UniredUpdateClientDebitRequestDTO $params): UniredResponseDTO
    {
        return $this->makeRequest(self::CLIENT_DEBIT_UPDATE, $params->toArray());
    }

    public function findClient(UniredFindClientRequestDTO $params): UniredResponseDTO
    {
        return $this->makeRequest(self::CLIENT_FIND, $params->toArray());
    }

    public function findMultipleClients(UniredFindClientsRequestDTO $params): UniredResponseDTO
    {
        return $this->makeRequest(self::CLIENT_FIND_MULTIPLE, $params->toArray());
    }

    public function addClientPhone(UniredAddClientPhoneRequestDTO $params): UniredResponseDTO
    {
        return $this->makeRequest(self::CLIENT_PHONE_ADD, $params->toArray());
    }

    public function getAllClients(UniredPaginationRequestDTO $params): UniredResponseDTO
    {
        $params->filial_id = tenant('id');

        return $this->makeRequest(self::CLIENT_ALL, $params->toArray());
    }

    public function toggleClientAuto(UniredToggleClientAutoRequestDTO $params): UniredResponseDTO
    {
        return $this->makeRequest(self::CLIENT_AUTO_TOGGLE, $params->toArray());
    }

    public function getPayments(UniredGetPaymentsPaginationRequestDTO $params): UniredResponseDTO
    {
        return $this->makeRequest(self::PAYMENT_GET, $params->toArray());
    }

    public function registerPayment(UniredRegisterPaymentRequestDTO $params): UniredResponseDTO
    {
        return $this->makeRequest(self::PAYMENT_REGISTER, $params->toArray());
    }

    public function cancelPayment(UniredCancelPaymentRequestDTO $params): UniredResponseDTO
    {
        return $this->makeRequest(self::PAYMENT_CANCEL, $params->toArray());
    }

    public function changePaymentSendStatus(UniredChangePaymentSendStatusRequestDTO $params): UniredResponseDTO
    {
        return $this->makeRequest(self::PAYMENT_CHANGE_SEND_STATUS, $params->toArray());
    }

    public function checkWebhook(): UniredResponseDTO
    {
        return $this->makeRequest(self::CHECK_WEBHOOK, []);
    }

    public function setWebhook(UniredSetWebhookDTO $params): UniredResponseDTO
    {
        return $this->makeRequest(self::SET_WEBHOOK, $params->toArray());
    }

    public function createClients(UniredCreateClientRequestDTO $params): UniredResponseDTO
    {
        return $this->makeRequest(self::CLIENT_CREATE, [
            'clients' => [$params->toArray()],
        ]);
    }

    public function addClientPhones(UniredAddClientPhonesRequestDTO $params)
    {
        return $this->makeRequest(self::ADD_CLIENT_PHONE, [
            'clients' => [$params->toArray()],
        ]);
    }

    public function createContracts(UniredCreateContractRequestDTO $params)
    {
        return $this->makeRequest(self::CONTRACT_CREATE, [
            'contracts' => [$params->toArray()],
        ]);
    }

    public function updateContract(UniredUpdateContractRequestDTO $params): UniredResponseDTO
    {
        return $this->makeRequest(self::CONTRACT_UPDATE, $params->toArray());
    }

    public function deleteContract(UniredContractLoanIdDTO $params): UniredResponseDTO
    {
        return $this->makeRequest(self::CONTRACT_DELETE, $params->toArray());
    }

    public function getTransactions(UniredGetPaymentsPaginationRequestDTO $params): UniredResponseDTO
    {
        return $this->makeRequest(self::TRANSACTION_GET, $params->toArray());
    }

    public function getPinflCardCounts(string $pinfl): UniredClientPinflCardCountDTO
    {
        $response = $this->makeRequest(self::CARD_COUNT, [
            'pinfl' => [$pinfl],
        ]);
        if ($response->isSuccess()) {
            $result = $response->result;
            foreach ($result as $pinfl => $item) {
                return UniredClientPinflCardCountDTO::from($item);
            }
        }

        return new UniredClientPinflCardCountDTO(0, 0);
    }

    public function getCards(UniredClientPinflGetCardRequestDTO $params): UniredResponseDTO
    {
        return $this->makeRequest(self::CARD_INFO, $params->toArray());
    }

    public function updateHumoCards(UniredClientPinflCardUpdateRequestDTO $params)
    {
        return $this->makeRequest(self::PROCESSING_HUMO_SEARCH, $params->toArray());
    }

    public function updateUzcardCards(UniredClientPinflCardUpdateRequestDTO $params)
    {
        return $this->makeRequest(self::PROCESSING_UZCARD_SEARCH, $params->toArray());
    }

    private function httpRequest(array $data, string $httpMethod, string $path): UniredResponseDTO
    {
        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->token,
        ];
        $response = Http::withHeaders($headers)->$httpMethod($this->baseUrl . $path, $data);
        $headers['Authorization'] = 'Bearer ****' . substr($this->token, -9);
        $this->logRequest(new HttpRequestLogDTO(
            method: $httpMethod,
            url: $this->baseUrl . $path,
            status: $response->status(),
            response: $response->json(),
            duration: $response->transferStats?->getTransferTime(),
            headers: $headers,
            body: $data,
        ));

        return UniredResponseDTO::from($response->json());
    }

    private function makeRequest($method, $params, $httpMethod = 'post'): UniredResponseDTO
    {
        $response = $this->httpRequest(['method' => $method, 'params' => $params], $httpMethod, '');

        if (! $response->isSuccess()) {
            throw new UniredException($response->error->message);
        }

        return $response;
    }
}
