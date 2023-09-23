<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Resources\Admin\UserInfoResource;
use App\Http\Resources\DeviceInfoResource;
use App\Models\Company\PersonalAccessToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;

class ProfileController extends Controller
{
    #[OA\Get(
        path: '/api/v1/profile/me',
        operationId: 'profileMe',
        description: 'Profile me route',
        summary: 'Profile me route',
        tags: ['Profile'],
        responses: [
            new OA\Response(
                response: '200',
                description: 'Success',
                content: new OA\JsonContent(
                    example: [
                        'success' => true,
                        'message' => null,
                        'data' => [
                            'username' => 'admin',
                            'first_name' => 'Yura',
                            'last_name' => 'Sultonov',
                            'phone' => null,
                            'status' => 1,
                            'avatar' => 'no-image.webp',
                        ],
                    ]
                )
            ),
        ]
    )]
    public function me():JsonResponse
    {
        return ApiResponse::success(UserInfoResource::make(Auth::user()));
    }

    #[OA\Get(
        path: '/api/v1/profile/devices',
        operationId: 'profileDevices',
        description: 'My devices route',
        summary: 'My devices route',
        tags: ['Profile'],
        responses: [
            new OA\Response(
                response: '200',
                description: 'Success',
                content: new OA\JsonContent(
                    example: [
                        'success' => true,
                        'message' => null,
                        'data' => [
                            [
                                'id' => 1,
                                'name' => 'iPhone 12 Pro Max',
                                'device_name' => '2021-09-30T09:00:00.000000Z',
                                'device_ip' => ''
                            ],
                            [
                                'id' => 2,
                                'name' => 'iPhone 11 Pro',
                                'device_name' => '',
                                'device_ip' => ''
                            ],
                        ],
                    ]
                )
            ),
        ]
    )]
    public function devices():JsonResponse
    {
        return ApiResponse::success(DeviceInfoResource::collection(Auth::user()->tokens));
    }

    #[OA\Delete(
        path: '/api/v1/profile/revoke-device/{id}',
        operationId: 'profileRevokeDevice',
        description: 'Revoke device route',
        summary: 'Revoke device route',
        tags: ['Profile'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'Device id',
                in: 'path',
                required: true,
            )
        ],
        responses: [
            new OA\Response(
                response: '200',
                description: 'Success',
                content: new OA\JsonContent(
                    example: [
                        'success' => true,
                        'message' => null,
                        'data' => true
                    ]
                )
            ),
            new OA\Response(
                response: '404',
                description: 'Not found'
            ),
        ]
    )]
    public function revokeDevice(PersonalAccessToken $personalAccessToken): JsonResponse
    {
        return ApiResponse::success($personalAccessToken->delete());
    }
}
