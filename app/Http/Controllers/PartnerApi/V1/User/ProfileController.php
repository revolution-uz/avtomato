<?php

namespace App\Http\Controllers\PartnerApi\V1\User;

use App\Helpers\Swagger\OpenApiJsonArrayResponse;
use App\Helpers\Swagger\OpenApiJsonResponse;
use App\Helpers\Swagger\OpenApiNotFoundResponse;
use App\Helpers\Swagger\OpenApiSingleDataResponse;
use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Resources\DeviceInfoResource;
use App\Http\Resources\UserInfoResource;
use App\Models\Company\PersonalAccessToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;

class ProfileController extends Controller
{
    #[OA\Get(
        path: '/api/v1/profile/me',
        description: 'Profile me route',
        tags: ['Profile'],
        responses: [
            new OpenApiJsonResponse(resourceName: 'UserInfoResource'),
        ]
    )]
    public function me(): JsonResponse
    {
        return ApiResponse::success(UserInfoResource::make(Auth::user()));
    }

    #[OA\Get(
        path: '/api/v1/profile/devices',
        description: 'My devices route',
        tags: ['Profile'],
        responses: [
            new OpenApiJsonArrayResponse(resourceName: 'DeviceInfoResource'),
        ]
    )]
    public function devices(): JsonResponse
    {
        return ApiResponse::success(DeviceInfoResource::collection(Auth::user()->tokens));
    }

    #[OA\Delete(
        path: '/api/v1/profile/revoke-device/{id}',
        description: 'Revoke device route',
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
            new OpenApiSingleDataResponse(data: 'true', type: 'boolean'),
            new OpenApiNotFoundResponse(),
        ]
    )]
    public function revokeDevice(PersonalAccessToken $personalAccessToken): JsonResponse
    {
        return ApiResponse::success($personalAccessToken->delete());
    }
}
