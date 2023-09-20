<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Requests\RevokeTokenRequest;
use App\Models\Company\PersonalAccessToken;
use App\Models\Company\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function me() {
        return ApiResponse::success(['user' =>  Auth::user()->toApiArray()]);
    }

    public function devices() {
        return ApiResponse::success(['devices' => Auth::user()->tokens()]);
    }
    public function revokeDevice(RevokeTokenRequest $request): JsonResponse
    {
        return ApiResponse::success($this->passportTokenService
            ->revokeAccessAndRefreshToken(tokenId: $request->validated('token')));
    }
}
