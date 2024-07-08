<?php

namespace App\Http\Controllers\PartnerApi\V1;

use App\Helpers\Swagger\OpenApiDataResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\LoginResource;
use App\Http\Resources\StatusResource;
use App\Models\Company\CompanyAdmin;
use App\Models\Company\PersonalAccessToken;
use Illuminate\Auth\AuthenticationException;
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
            new OpenApiDataResponse('LoginResource'),
            new OA\Response(
                response: '401',
                description: 'Error',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'message',
                            type: 'string',
                            example: 'Login or password is wrong'
                        ),
                    ],
                )
            ),
        ]
    )]
    public function login(LoginRequest $request): LoginResource
    {
        $admin = CompanyAdmin::query()->where('username', $request->username)->first();
        if (! $admin || ! Hash::check($request->password, $admin->password)) {
            throw new AuthenticationException('Login or password is wrong');
        }

        if (! $request->hasHeader('Device-Name') || ! $request->hasHeader('Device-Type') || ! $request->hasHeader('Device-OS')) {
            throw new AuthenticationException('Login or password is wrong');
        }

        return new LoginResource($admin->createTokenWithData(
            Str::substr($request->header('Device-Name'), 0, 255),
            [
                'device_type' => Str::substr($request->header('Device-Type'), 0, 32),
                'device_os' => Str::substr($request->header('Device-OS'), 0, 32),
                'device_ip' => $request->ip(),
            ]
        ));
    }

    #[OA\Post(
        path: '/api/v1/logout',
        description: 'Logout route',
        tags: ['Auth'],
        responses: [
            new OpenApiDataResponse('StatusResource'),
        ]
    )]
    public function logout(): StatusResource
    {
        /** @var PersonalAccessToken $personalAccessToken */
        $personalAccessToken = auth()->user()->currentAccessToken();

        return new StatusResource(boolval(auth()->user()->tokens()->where('id', $personalAccessToken->id)->delete()));
    }
}
