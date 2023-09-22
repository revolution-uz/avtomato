<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\SettingsEnum;
use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Models\Company\Settings;
use Illuminate\Http\JsonResponse;

class InfoController extends Controller
{
    public function settings(): JsonResponse
    {
        return ApiResponse::success(Settings::query()->whereIn('name', SettingsEnum::partnerParameters())
            ->get());
    }
}
