<?php

namespace App\Http\Controllers\PartnerApi\V1\User;

use App\Helpers\Swagger\OpenApiArrayResponse;
use App\Helpers\Swagger\OpenApiDataResponse;
use App\Helpers\Swagger\OpenApiNotFoundResponse;
use App\Helpers\Swagger\OpenApiPageParameter;
use App\Helpers\Swagger\OpenApiPerPageParameter;
use App\Helpers\Swagger\OpenApiSearchParameter;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\AvatarRequest;
use App\Http\Requests\User\DeviceRevokeRequest;
use App\Http\Requests\User\PasswordRequest;
use App\Http\Requests\User\ProfileMeRequest;
use App\Http\Requests\User\UsersDevicesRequest;
use App\Http\Requests\User\UserThemeRequest;
use App\Http\Requests\User\UserUpdateRequest;
use App\Http\Requests\UserLangRequest;
use App\Http\Resources\DeviceInfoResource;
use App\Http\Resources\StatusResource;
use App\Http\Resources\User\SingleUserResource;
use App\Models\Company\PersonalAccessToken;
use App\Services\User\UserService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
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
            new OpenApiDataResponse(resourceName: 'SingleUserResource'),
        ]
    )]
    public function me(ProfileMeRequest $request): SingleUserResource
    {
        return new SingleUserResource(auth()->user());
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
            new OpenApiDataResponse(resourceName: 'StatusResource'),
        ],
    )]
    public function changePassword(PasswordRequest $request)
    {
        $this->userService->updatePassword(auth()->user(), $request->get('password'));

        return new StatusResource(true);
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
            new OpenApiDataResponse(resourceName: 'SingleUserResource'),
        ],
    )]
    public function changeTheme(UserThemeRequest $request)
    {
        return new SingleUserResource($this->userService->updateTheme(auth()->user(), $request->getData()));
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
            new OpenApiDataResponse(resourceName: 'SingleUserResource'),
        ],
    )]
    public function changeLanguage(UserLangRequest $request)
    {
        $user = auth()->user();
        $user?->update(['language' => $request->language]);

        return new SingleUserResource($user);
    }

    #[OA\Put(
        path: '/api/v1/profile/update',
        description: 'Profile update route',
        requestBody: new OA\RequestBody(
            description: 'Profile update request body',
            required: true,
            content: new OA\JsonContent(
                ref: '#/components/schemas/UserUpdateRequest'
            ),
        ),
        tags: ['Profile'],
        responses: [
            new OpenApiDataResponse(resourceName: 'SingleUserResource'),
        ],
    )]
    public function update(UserUpdateRequest $request)
    {
        return new SingleUserResource($this->userService->updateDetails(auth()->user(), $request->validated()));
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
            new OpenApiDataResponse(resourceName: 'SingleUserResource'),
        ],
    )]
    public function uploadAvatar(AvatarRequest $request)
    {
        return new SingleUserResource($this->userService->updateAvatar(auth()->user(), $request->file('avatar')));
    }

    #[OA\Get(
        path: '/api/v1/profile/devices',
        description: 'My devices route',
        tags: ['Profile'],
        parameters: [
            new OpenApiPageParameter,
            new OpenApiPerPageParameter,
            new OpenApiSearchParameter,
        ],
        responses: [
            new OpenApiArrayResponse(resourceName: 'DeviceInfoResource'),
        ]
    )]
    public function devices(UsersDevicesRequest $request): AnonymousResourceCollection
    {
        $request->sortables = ['id', 'name', 'device_ip', 'device_type', 'device_os', 'last_used_at']; // TODO add all fields, it's just for testing

        return DeviceInfoResource::collection(
            auth()->user()->tokens()
                ->when($request->search, function (Builder $q) use ($request) {
                    $search = '%' . $request->search . '%';
                    $q->where(function (Builder $query) use ($search) {
                        $query->where('name', 'ILIKE', $search)
                            ->orWhere('device_ip', 'ILIKE', $search)
                            ->orWhere('device_type', 'ILIKE', $search)
                            ->orWhere('device_os', 'ILIKE', $search);
                    });
                })
                ->when($request->isSortable(), function (Builder $q) use ($request) {
                    $q->orderBy($request->sortField(), $request->sortDirection());
                })
                ->paginate(
                    $request->per_page,
                    page: $request->page
                ))->additional(['sortables' => $request->sortables]);
    }

    #[OA\Delete(
        path: '/api/v1/profile/revoke-device',
        description: 'Revoke device route',
        requestBody: new OA\RequestBody(
            description: 'Revoke device request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/DeviceRevokeRequest')
        ),
        tags: ['Profile'],
        responses: [
            new OpenApiDataResponse(resourceName: 'StatusResource'),
            new OpenApiNotFoundResponse,
        ]
    )]
    public function revokeDevice(DeviceRevokeRequest $request): StatusResource
    {
        PersonalAccessToken::query()->whereIn('id', $request->ids)->delete();

        return new StatusResource(true);
    }
}
