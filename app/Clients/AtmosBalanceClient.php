<?php

namespace App\Clients;

use App\Enums\ClientCodeEnum;
use App\Enums\FeatureEnum;

class AtmosBalanceClient extends AtmosCardClient
{
    public function __construct()
    {
        parent::__construct();
        $this->baseUrl = config('clients.atmos.card.base_url');
        $this->channel = 'atmos-balance-client';
        $this->featureName = FeatureEnum::ATMOS_CARD_PAY;
        $this->isFeature = false;
        $this->store_id = (int) config('clients.atmos.card.store_id');
        $this->username = config('clients.atmos.card.username');
        $this->password = config('clients.atmos.card.password');
    }

    public function getClientCode(): ClientCodeEnum
    {
        return ClientCodeEnum::ATMOS_BALANCE_PAY;
    }
}
