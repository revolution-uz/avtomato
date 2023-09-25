<?php

namespace App\Http\Controllers\PartnerApi\V1\User;

use App\Helpers\Swagger\OpenApiJsonArrayResponse;
use App\Helpers\Swagger\OpenApiJsonResponse;
use App\Helpers\Swagger\OpenApiNotFoundResponse;
use App\Helpers\Swagger\OpenApiSingleDataResponse;
use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Requests\User\PasswordRequest;
use App\Http\Requests\User\UserThemeRequest;
use App\Http\Requests\User\UserUpdateRequest;
use App\Http\Resources\DeviceInfoResource;
use App\Http\Resources\UserResource;
use App\Models\Company\Attachment;
use App\Models\Company\PersonalAccessToken;
use App\Models\Company\Phone;
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
            new OpenApiJsonResponse(resourceName: 'UserResource'),
        ]
    )]
    public function me(): JsonResponse
    {
        return ApiResponse::success(UserResource::make(Auth::user()));
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
            new OpenApiJsonResponse(resourceName: 'UserResource'),
        ],
    )]
    public function update(UserUpdateRequest $request)
    {
        if ($request->avatar) {
            $avatar = Attachment::whereId($request->avatar)->first();
            auth()->user()?->avatars()->save($avatar);
        }
        if ($request->phone) {
            $phone = Phone::firstOrCreate(['phone' => $request->phone]);
            auth()->user()?->phones()->save($phone);
        }
        $attributes = $request->validated();
        unset($attributes['avatar'], $attributes['phone']);
        auth()->user()?->update($attributes);

        return ApiResponse::success(UserResource::make(auth()->user()));
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
            new OpenApiSingleDataResponse(data: 'true', type: 'boolean')
        ],
    )]
    public function changePassword(PasswordRequest $request)
    {
        $user = auth()->user();
        $user->password = bcrypt($request->get('password'));
        $user->save();

        return ApiResponse::success(true);
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
            new OpenApiSingleDataResponse(data: 'true', type: 'boolean')
        ],
    )]
    public function changeTheme(UserThemeRequest $request)
    {
        $user = auth()->user();
        $user?->update(['template_theme' => $request->getData(), 'language' => $request->language]);

        return ApiResponse::success(true);
    }
}
