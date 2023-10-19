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
use Illuminate\Support\Str;
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
        parameters: [
            new OA\Parameter(name: 'Device-Name', in: 'header', required: true, example: 'Mozilla/5.0 (Linux; Android 10; SM-A205U) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/93.0.4577.82 Mobile Safari/537.36'),
            new OA\Parameter(name: 'Device-Type', in: 'header', required: true, example: 'Mobile'),
            new OA\Parameter(name: 'Device-OS', in: 'header', required: true, example: 'Android'),
        ],
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

        if (! $request->hasHeader('Device-Name') || ! $request->hasHeader('Device-Type') || ! $request->hasHeader('Device-OS')) {
            return ApiResponse::error('Login or password is wrong');
        }

        return ApiResponse::success(
            $user->createTokenWithData(
                Str::substr($request->header('Device-Name'), 0, 255),
                [
                    'device_type' => Str::substr($request->header('Device-Type'), 0, 32),
                    'device_os' => Str::substr($request->header('Device-OS'), 0, 32),
                    'device_ip' => $request->ip(),
                ]
            )
        );
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
