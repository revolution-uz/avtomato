<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Company\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $user = User::query()->where('phone', $request->phone)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response([
                'message' => 'Bad credentials',
            ], 401);
        }
        return $user->createToken($request->device_name)->plainTextToken;
    }

    public function logout(): JsonResponse
    {
        $user = Auth::user();
        return ApiResponse::success(
            Auth::user()->tokens()->where('id', $user->currentAccessToken()->id)->delete()
        );
    }
}
