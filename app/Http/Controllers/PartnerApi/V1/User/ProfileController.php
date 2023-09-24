<?php

namespace App\Http\Controllers\PartnerApi\V1\User;

use App\Helpers\Swagger\OpenApiJsonArrayResponse;
use App\Helpers\Swagger\OpenApiJsonResponse;
use App\Helpers\Swagger\OpenApiNotFoundResponse;
use App\Helpers\Swagger\OpenApiSingleDataResponse;
use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Requests\PasswordRequest;
use App\Http\Requests\ProfileRequest;
use App\Http\Resources\DeviceInfoResource;
use App\Http\Resources\ProfileResource;
use App\Models\Company\PersonalAccessToken;
use App\Models\Company\User;
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
            new OpenApiJsonResponse(resourceName: 'ProfileResource'),
        ]
    )]
    public function me(): JsonResponse
    {
        return ApiResponse::success(ProfileResource::make(Auth::user()));
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

    #[OA\Post(
        path: '/api/v1/profile/me',
        description: 'Profile me route',
        requestBody: new OA\RequestBody(
            description: 'Profile update request body',
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(ref: '#/components/schemas/ProfileRequest')
            ),
        ),
        tags: ['Profile'],
        responses: [
            new OpenApiJsonResponse(resourceName: 'ProfileResource'),
        ],
    )]
    public function update(ProfileRequest $request)
    {
        if ($request->hasFile('avatar')) {
            $photo = $request->file('avatar');
            $file_name = $request->user()->id . '.' . $photo->extension();
            $path = $photo->storeAs(User::AVATAR_FOLDER, $file_name, ['disk' => 'public']);
        }

        $data = $request->validated();
        unset($data['avatar']);
        if (isset($path)) {
            $data['avatar'] = $path;
        }

        auth()->user()?->update($data);

        return ApiResponse::success(ProfileResource::make(auth()->user()));
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
}
