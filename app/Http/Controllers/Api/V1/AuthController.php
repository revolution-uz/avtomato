<?php

namespace App\Http\Controllers\Api\V1;

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
            return ApiResponse::error('Login or password is wrong');
        }
        return ApiResponse::success($user->createTokenWithDeviceName($request->device_name, $request->ip()));
    }

    public function logout(): JsonResponse
    {
        return ApiResponse::success(
            Auth::user()->tokens()->where('id', Auth::user()->currentAccessToken()->id)->delete()
        );
    }
}
