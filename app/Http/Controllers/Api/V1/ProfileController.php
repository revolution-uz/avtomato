<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\RevokeTokenRequest;
use Illuminate\Http\JsonResponse;

class ProfileController extends Controller
{
    public function me() {
        return ApiResponse::success($this->passportTokenService
            ->revokeAccessAndRefreshToken(tokenId: $request->validated('token')));
    }
    public function revokeToken(RevokeTokenRequest $request): JsonResponse
    {
        return ApiResponse::success($this->passportTokenService
            ->revokeAccessAndRefreshToken(tokenId: $request->validated('token')));
    }
}
