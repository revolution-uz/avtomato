<?php

namespace App\Http\Controllers\PartnerApi\V1;

use App\Helpers\Swagger\OpenApiErrorResponse;
use App\Helpers\Swagger\OpenApiSingleDataResponse;
use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Requests\LoginRequest;
use App\Models\Company\PersonalAccessToken;
use App\Models\Company\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use OpenApi\Attributes as OA;

class AuthController extends Controller
{
    #[OA\Post(
        path: '/api/v1/login',
        description: 'Login route',
        security: [],
        requestBody: new OA\RequestBody(
            description: 'Login request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/LoginRequest')
        ),
        tags: ['Auth'],
        responses: [
            new OpenApiSingleDataResponse(data: '1|DoeKLT28Gj1fxDGC3zoIYH4ZVseZxXkfFSmYAnla', type: 'string'),
            new OpenApiErrorResponse(message: 'Login or password is wrong'),
        ]
    )]
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::query()->where('username', $request->username)->first();
        if (! $user || ! Hash::check($request->password, $user->password)) {
            return ApiResponse::error('Login or password is wrong');
        }

        return ApiResponse::success($user->createTokenWithDeviceName($request->device_name, $request->ip()));
    }

    #[OA\Post(
        path: '/api/v1/logout',
        description: 'Logout route',
        tags: ['Auth'],
        responses: [
            new OpenApiSingleDataResponse(data: 'true', type: 'integer'),
        ]
    )]
    public function logout(): JsonResponse
    {
        /** @var PersonalAccessToken $personalAccessToken */
        $personalAccessToken = auth()->user()->currentAccessToken();

        return ApiResponse::success(
            boolval(auth()->user()->tokens()->where('id', $personalAccessToken->id)->delete())
        );
    }
}
