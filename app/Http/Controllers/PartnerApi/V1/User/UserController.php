<?php

namespace App\Http\Controllers\PartnerApi\V1\User;

use App\Helpers\Swagger\OpenApiArrayResponse;
use App\Helpers\Swagger\OpenApiDataResponse;
use App\Helpers\Swagger\OpenApiIdParameter;
use App\Helpers\Swagger\OpenApiNotFoundResponse;
use App\Helpers\Swagger\OpenApiPageParameter;
use App\Helpers\Swagger\OpenApiPerPageParameter;
use App\Helpers\Swagger\OpenApiSearchParameter;
use App\Helpers\Swagger\OpenApiSortParameter;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\AttachRoleRequest;
use App\Http\Requests\User\AvatarRequest;
use App\Http\Requests\User\DeviceRevokeRequest;
use App\Http\Requests\User\PasswordRequest;
use App\Http\Requests\User\UserCreateRequest;
use App\Http\Requests\User\UserFilterRequest;
use App\Http\Requests\User\UsersDeleteRequest;
use App\Http\Requests\User\UsersDevicesRequest;
use App\Http\Requests\User\UserThemeRequest;
use App\Http\Requests\User\UserUpdateRequest;
use App\Http\Requests\User\UserViewRequest;
use App\Http\Resources\DeviceInfoResource;
use App\Http\Resources\StatusResource;
use App\Http\Resources\User\SingleUserResource;
use App\Http\Resources\User\UserListResource;
use App\Models\Company\PersonalAccessToken;
use App\Models\Company\User;
use App\Services\User\UserService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use OpenApi\Attributes as OA;

class UserController extends Controller
{
    public function __construct(
        protected UserService $userService
    ) {}

    #[OA\Get(
        path: '/api/v1/users',
        description: 'Users route',
        tags: ['Users'],
        parameters: [
            new OpenApiPageParameter,
            new OpenApiPerPageParameter,
            new OpenApiSearchParameter,
            new OpenApiSortParameter,
        ],
        responses: [
            new OpenApiArrayResponse(resourceName: 'UserListResource', withPagination: true, withSortables: true),
        ]
    )]
    public function index(UserFilterRequest $request)
    {
        $request->sortables = ['id', 'username', 'first_name', 'last_name', 'created_at', 'updated_at'];

        return UserListResource::collection(
            User::query()
                ->when($request->search, function (Builder $q) use ($request) {
                    $search = '%' . $request->search . '%';
                    $q->where(function (Builder $query) use ($search) {
                        $query->where('username', 'ILIKE', $search)
                            ->orWhere('first_name', 'ILIKE', $search)
                            ->orWhere('last_name', 'ILIKE', $search);
                    });
                })
                ->when($request->isSortable(), function (Builder $q) use ($request) {
                    $q->orderBy($request->sortField(), $request->sortDirection());
                })
                ->paginate($request->per_page)
        )->additional(['sortables' => $request->sortables]);
    }

    #[OA\Get(
        path: '/api/v1/users/{id}',
        description: 'Users get single route',
        tags: ['Users'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'User ID',
                in: 'path',
                required: true
            ),
        ],
        responses: [
            new OpenApiDataResponse(resourceName: 'SingleUserResource'),
            new OpenApiNotFoundResponse,
        ]
    )]
    public function getSingle(int $id, UserViewRequest $request): SingleUserResource
    {
        return new SingleUserResource(User::findOrFail($id));
    }

    #[OA\Post(
        path: '/api/v1/users',
        description: 'Users create route',
        requestBody: new OA\RequestBody(
            description: 'Users create request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/UserCreateRequest')
        ),
        tags: ['Users'],
        responses: [
            new OpenApiDataResponse(resourceName: 'SingleUserResource', response: '201'),
        ]
    )]
    public function create(UserCreateRequest $request): SingleUserResource
    {
        $model = new User;
        $model->fill($request->validated());
        $model->password = bcrypt($request->password);
        $model->saveOrFail();

        return new SingleUserResource($model);
    }

    #[OA\Put(
        path: '/api/v1/users/{id}',
        description: 'Users create route',
        requestBody: new OA\RequestBody(
            description: 'Users create request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/UserUpdateRequest')
        ),
        tags: ['Users'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'User ID',
                in: 'path',
                required: true
            ),
        ],
        responses: [
            new OpenApiDataResponse(resourceName: 'SingleUserResource'),
            new OpenApiNotFoundResponse,
        ]
    )]
    public function update(int $id, UserUpdateRequest $request): SingleUserResource
    {
        $user = User::findOrFail($id);
        $this->userService->updateDetails($user, $request->validated());

        return new SingleUserResource($user);
    }

    #[OA\Post(
        path: '/api/v1/users/{id}/change-password',
        description: 'Change password route',
        requestBody: new OA\RequestBody(
            description: 'Change password request body',
            required: true,
            content: new OA\JsonContent(
                ref: '#/components/schemas/PasswordRequest'
            ),
        ),
        tags: ['Users'],
        parameters: [
            new OpenApiIdParameter(modelName: 'User'),
        ],
        responses: [
            new OpenApiDataResponse(resourceName: 'SingleUserResource'),
        ],
    )]
    public function changePassword(int $id, PasswordRequest $request)
    {
        $user = User::findOrFail($id);

        return new SingleUserResource($this->userService->updatePassword($user, $request->get('password')));
    }

    #[OA\Post(
        path: '/api/v1/users/{id}/change-theme',
        description: 'Change theme route',
        requestBody: new OA\RequestBody(
            description: 'Change theme request body',
            required: true,
            content: new OA\JsonContent(
                ref: '#/components/schemas/UserThemeRequest'
            ),
        ),
        tags: ['Users'],
        parameters: [
            new OpenApiIdParameter(modelName: 'User'),
        ],
        responses: [
            new OpenApiDataResponse(resourceName: 'SingleUserResource'),
        ],
    )]
    public function changeTheme(int $id, UserThemeRequest $request)
    {
        $user = User::findOrFail($id);

        return new SingleUserResource($this->userService->updateTheme($user, $request->getData()));
    }

    #[OA\Post(
        path: '/api/v1/users/{id}/change-avatar',
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
    public function uploadAvatar(int $id, AvatarRequest $request)
    {
        $user = User::findOrFail($id);

        return new SingleUserResource($this->userService->updateAvatar($user, $request->file('avatar')));
    }

    #[OA\Get(
        path: '/api/v1/users/{id}/devices',
        description: 'My devices route',
        tags: ['Users'],
        parameters: [
            new OpenApiIdParameter(modelName: 'User'),
        ],
        responses: [
            new OpenApiArrayResponse(resourceName: 'DeviceInfoResource'),
        ]
    )]
    public function devices(int $id, UsersDevicesRequest $request): AnonymousResourceCollection
    {
        $user = User::findOrFail($id);

        return DeviceInfoResource::collection($user->tokens()->paginate(
            $request->per_page,
            page: $request->page
        ));
    }

    #[OA\Delete(
        path: '/api/v1/users/revoke-device/{id}',
        description: 'Revoke device route',
        tags: ['Users'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'Device id',
                in: 'path',
                required: true,
            ),
        ],
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

    #[OA\Delete(
        path: '/api/v1/users/{id}',
        description: 'Users delete route',
        tags: ['Users'],
        parameters: [
            new OpenApiIdParameter(modelName: 'User'),
        ],
        responses: [
            new OpenApiDataResponse(resourceName: 'StatusResource'),
        ]
    )]
    public function delete(UsersDeleteRequest $request): StatusResource
    {
        User::query()->whereIn('id', $request->ids)->delete();

        return new StatusResource(true);
    }

    #[OA\Post(
        path: '/api/v1/users/{id}/attach-role',
        description: 'Revoke device route',
        requestBody: new OA\RequestBody(
            description: 'Attach role request body',
            required: true,
            content: new OA\JsonContent(
                ref: '#/components/schemas/AttachRoleRequest'
            ),
        ),
        tags: ['Users'],
        parameters: [
            new OpenApiIdParameter(modelName: 'User'),
        ],
        responses: [
            new OpenApiDataResponse(resourceName: 'SingleUserResource'),
            new OpenApiNotFoundResponse,
        ]
    )]
    public function attachRole(int $id, AttachRoleRequest $request)
    {
        $user = User::where('id', $id)
            ->where('role_id', '!=', $request->role_id)
            ->firstOrFail();

        if ($user->id === auth()->id()) {
            abort(403, 'You can\'t change your own role');
        }

        $user->updateOrFail(['role_id' => $request->role_id]);

        return new SingleUserResource($user);
    }
}
