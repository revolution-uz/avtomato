<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Requests\LoginRequest;
use App\Models\Company\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use OpenApi\Attributes as OA;

class AuthController extends Controller
{
    #[OA\Post(
        path: '/api/v1/login',
        operationId: 'login',
        description: 'Login route',
        summary: 'Login route',
        requestBody: new OA\RequestBody(
            description: 'Login request body',
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'username', type: 'string', example: 'admin'),
                    new OA\Property(property: 'password', type: 'string', example: 'password'),
                    new OA\Property(property: 'device_name', type: 'string', example: 'iPhone 12 Pro Max'),
                ]
            )
        ),
        tags: ['Auth'],
        responses: [
            new OA\Response(
                response: '200',
                description: 'Success',
                content: new OA\JsonContent(
                    example: [
                        'success' => true,
                        'message' => null,
                        'data' => '1|DoeKLT28Gj1fxDGC3zoIYH4ZVseZxXkfFSmYAnla',
                    ]
                )
            ),
        ]
    )]
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::query()->where('username', $request->username)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return ApiResponse::error('Login or password is wrong');
        }
        return ApiResponse::success($user->createTokenWithDeviceName($request->device_name, $request->ip()));
    }

    #[OA\Post(
        path: '/api/v1/logout',
        operationId: 'logout',
        description: 'Logout route',
        summary: 'Logout route',
        security: [['bearerAuth' => []]],
        tags: ['Auth'], responses: [
            new OA\Response(
                response: '200',
                description: 'Success',
                content: new OA\JsonContent(
                    example: [
                        'success' => true,
                        'message' => null,
                        'data' => 1,
                    ]
                )
            ),
        ]
    )]
    public function logout(): JsonResponse
    {
        return ApiResponse::success(
            Auth::user()->tokens()->where('id', Auth::user()->currentAccessToken()->id)->delete()
        );
    }
}
