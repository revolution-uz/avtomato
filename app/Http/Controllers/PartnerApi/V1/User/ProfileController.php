<?php

namespace App\Http\Controllers\PartnerApi\V1\User;

use App\Helpers\Swagger\OpenApiJsonArrayResponse;
use App\Helpers\Swagger\OpenApiJsonResponse;
use App\Helpers\Swagger\OpenApiNotFoundResponse;
use App\Helpers\Swagger\OpenApiSingleDataResponse;
use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Requests\User\AvatarRequest;
use App\Http\Requests\User\PasswordRequest;
use App\Http\Requests\User\UserThemeRequest;
use App\Http\Requests\User\UserUpdateRequest;
use App\Http\Requests\UserLangRequest;
use App\Http\Resources\DeviceInfoResource;
use App\Http\Resources\User\SingleUserResource;
use App\Models\Company\PersonalAccessToken;
use App\Services\User\UserService;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class ProfileController extends Controller
{
    public function __construct(
        protected UserService $userService
    ) {
    }

    #[OA\Get(
        path: '/api/v1/profile/me',
        description: 'Profile me route',
        tags: ['Profile'],
        responses: [
            new OpenApiJsonResponse(resourceName: 'SingleUserResource'),
        ]
    )]
    public function me(): JsonResponse
    {
        return ApiResponse::success(SingleUserResource::make(auth()->user()));
    }

    #[OA\Put(
        path: '/api/v1/profile/me',
        description: 'Profile me route',
        requestBody: new OA\RequestBody(
            description: 'Profile update request body',
            required: true,
            content: new OA\JsonContent(
                ref: '#/components/schemas/UserUpdateRequest'
            ),
        ),
        tags: ['Profile'],
        responses: [
            new OpenApiJsonResponse(resourceName: 'SingleUserResource'),
        ],
    )]
    public function update(UserUpdateRequest $request)
    {
        if ($this->userService->updateDetails(auth()->user(), $request)) {
            return ApiResponse::success(SingleUserResource::make(auth()->user()));
        }

        return ApiResponse::error('User not updated');
    }

    #[OA\Post(
        path: '/api/v1/profile/change-password',
        description: 'Change password route',
        requestBody: new OA\RequestBody(
            description: 'Change password request body',
            required: true,
            content: new OA\JsonContent(
                ref: '#/components/schemas/PasswordRequest'
            ),
        ),
        tags: ['Profile'],
        responses: [
            new OpenApiSingleDataResponse(data: 'true', type: 'boolean'),
        ],
    )]
    public function changePassword(PasswordRequest $request)
    {
        if ($this->userService->updatePassword(auth()->user(), $request)) {
            return ApiResponse::success(true);
        }

        return ApiResponse::error('Password not updated');
    }

    #[OA\Post(
        path: '/api/v1/profile/change-theme',
        description: 'Change theme route',
        requestBody: new OA\RequestBody(
            description: 'Change theme request body',
            required: true,
            content: new OA\JsonContent(
                ref: '#/components/schemas/UserThemeRequest'
            ),
        ),
        tags: ['Profile'],
        responses: [
            new OpenApiJsonResponse(resourceName: 'SingleUserResource'),
        ],
    )]
    public function changeTheme(UserThemeRequest $request)
    {
        if ($this->userService->updateTheme(auth()->user(), $request)) {
            return ApiResponse::success(SingleUserResource::make(auth()->user()));
        }

        return ApiResponse::error('Theme not updated');
    }

    #[OA\Post(
        path: '/api/v1/profile/change-lang',
        description: 'Change language route',
        requestBody: new OA\RequestBody(
            description: 'Change language request body',
            required: true,
            content: new OA\JsonContent(
                ref: '#/components/schemas/UserLangRequest'
            ),
        ),
        tags: ['Profile'],
        responses: [
            new OpenApiJsonResponse(resourceName: 'SingleUserResource'),
        ],
    )]
    public function changeLanguage(UserLangRequest $request)
    {
        $user = auth()->user();
        $user?->update(['language' => $request->language]);

        return ApiResponse::success(SingleUserResource::make($user));
    }

    #[OA\Post(
        path: '/api/v1/profile/change-avatar',
        description: 'Change avatar route',
        requestBody: new OA\RequestBody(
            description: 'Attachment upload body. Send as multipart/form-data',
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(ref: '#/components/schemas/AvatarRequest')
            ),
        ),
        tags: ['Profile'],
        responses: [
            new OpenApiJsonResponse(resourceName: 'SingleUserResource'),
        ],
    )]
    public function uploadAvatar(AvatarRequest $request)
    {
        if ($this->userService->updateAvatar(auth()->user(), $request)) {
            return ApiResponse::success(SingleUserResource::make(auth()->user()));
        }

        return ApiResponse::error('Avatar not updated');
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
        return ApiResponse::success(DeviceInfoResource::collection(auth()->user()->tokens));
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
            ),
        ],
        responses: [
            new OpenApiSingleDataResponse(data: 'true', type: 'boolean'),
            new OpenApiNotFoundResponse,
        ]
    )]
    public function revokeDevice(int $id): JsonResponse
    {
        return ApiResponse::success(PersonalAccessToken::findOrFail($id)->delete());
    }
}
