<?php

namespace App\Clients;

use App\DTOs\Atmos\AtmosAuthTokenDTO;
use App\Enums\ClientCodeEnum;
use App\Enums\FeatureEnum;
use Illuminate\Support\Facades\Cache;

class AtmosBalanceClient extends AtmosCardClient
{
    public function __construct()
    {
        parent::__construct();
        $this->baseUrl = config('clients.atmos.card.base_url');
        $this->channel = 'atmos-balance-client';
        $this->featureName = FeatureEnum::ATMOS_CARD_PAY;
    }

    public function getClientCode(): ClientCodeEnum
    {
        return ClientCodeEnum::ATMOS_BALANCE_PAY;
    }

    protected function getActiveToken(): ?string
    {
        if (! tenancy()->initialized) {
            return null;
        }

        /** @var AtmosAuthTokenDTO|null $existingToken */
        $existingToken = Cache::get('atmos_balance_token');

        if (! $existingToken || $existingToken->expires_in <= time()) {
            $existingToken = $this->getNewToken(config('clients.atmos.card.username'), config('clients.atmos.card.password'));
            Cache::put('atmos_balance_token', $existingToken, $existingToken->expires_in - 60);
        }

        return $existingToken->token_type . ' ' . $existingToken->access_token;
    }

    protected function getStoreId(): int
    {
        return (int) config('clients.atmos.card.store_id');
    }
}
