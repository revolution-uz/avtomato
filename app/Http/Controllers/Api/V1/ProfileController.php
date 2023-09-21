<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Resources\Admin\UserInfoResource;
use App\Http\Resources\DeviceInfoResource;
use App\Models\Company\PersonalAccessToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function me():JsonResponse
    {
        return ApiResponse::success(UserInfoResource::make(Auth::user()));
    }

    public function devices():JsonResponse
    {
        return ApiResponse::success(DeviceInfoResource::collection(Auth::user()->tokens));
    }

    public function revokeDevice(PersonalAccessToken $personalAccessToken): JsonResponse
    {
        return ApiResponse::success($personalAccessToken->delete());
    }
}
