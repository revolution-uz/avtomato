<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Requests\LoginRequest;
use App\Models\Company\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::query()->where('username', $request->username)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return ApiResponse::error('Bad request', 401);
        }
        return ApiResponse::success(['access_token' => $user->createToken($request->device_name)->plainTextToken]);
    }

    public function logout(): JsonResponse
    {
        $user = Auth::user();
        return ApiResponse::success(
            Auth::user()->tokens()->where('id', $user->currentAccessToken()->id)->delete()
        );
    }
}
