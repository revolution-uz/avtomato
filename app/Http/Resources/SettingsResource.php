<?php

namespace App\Http\Resources;

use App\Enums\Main\SettingsEnum;
use App\Models\Settings;
use App\Services\User\BalanceService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'SettingsResource',
    title: 'SettingsResource',
    properties: [
        new OA\Property(property: 'app_maintenance_mode', type: 'boolean', example: 'true'),
        new OA\Property(property: 'web_maintenance_mode', type: 'boolean', example: 'true'),
        new OA\Property(property: 'app_min_version', type: 'int', example: '1'),
        new OA\Property(property: 'balance', type: 'int', example: '1000'),
    ],
)]
class SettingsResource extends JsonResource
{
    public function __construct($resource, private readonly BalanceService $balanceService)
    {
        parent::__construct($resource);
    }

    public function toArray(Request $request): array
    {
        return [
            'app_maintenance_mode' => boolval(Settings::getConfig(SettingsEnum::PARTNER_APP_MAINTENANCE_MODE)),
            'web_maintenance_mode' => boolval(Settings::getConfig(SettingsEnum::PARTNER_WEB_MAINTENANCE_MODE)),
            'app_min_version' => intval(Settings::getConfig(SettingsEnum::PARTNER_APP_MIN_VERSION)),
            'balance' => $this->balanceService->getBalance(),
        ];
    }
}
