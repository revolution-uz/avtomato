<?php

namespace App\Http\Controllers\PartnerApi\V1\User;

use App\Helpers\Swagger\OpenApiIdParameter;
use App\Helpers\Swagger\OpenApiJsonArrayResponse;
use App\Helpers\Swagger\OpenApiJsonResponse;
use App\Helpers\Swagger\OpenApiNotFoundResponse;
use App\Helpers\Swagger\OpenApiPageParameter;
use App\Helpers\Swagger\OpenApiPerPageParameter;
use App\Helpers\Swagger\OpenApiSearchParameter;
use App\Helpers\Swagger\OpenApiSingleDataResponse;
use App\Helpers\Swagger\OpenApiSortParameter;
use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Requests\User\AttachRoleRequest;
use App\Http\Requests\User\PasswordRequest;
use App\Http\Requests\User\UserCreateRequest;
use App\Http\Requests\User\UserFilterRequest;
use App\Http\Requests\User\UserThemeRequest;
use App\Http\Requests\User\UserUpdateRequest;
use App\Http\Resources\DeviceInfoResource;
use App\Http\Resources\User\SingleUserResource;
use App\Http\Resources\User\UserListResource;
use App\Models\Company\PersonalAccessToken;
use App\Models\Company\User;
use App\Services\User\UserService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Str;
use OpenApi\Attributes as OA;

class UserController extends Controller
{
    public function __construct(
        protected UserService $userService
    ) {
    }

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
            new OpenApiJsonArrayResponse(resourceName: 'UserListResource', withPagination: true, withSortables: true),
        ]
    )]
    public function index(UserFilterRequest $request): AnonymousResourceCollection
    {
        $sortables = ['id', 'username', 'first_name', 'last_name', 'created_at', 'updated_at'];

        return UserListResource::collection(
            User::query()
                ->when(
                    $request->search,
                    fn (Builder $q) => $q
                        ->where('username', 'ILIKE', '%' . $request->search . '%')
                        ->where('first_name', 'ILIKE', '%' . $request->search . '%')
                        ->where('last_name', 'ILIKE', '%' . $request->search . '%')
                )
                ->when(
                    $request->sort && in_array($request->sort, $sortables),
                    fn (Builder $q) => Str::startsWith($request->sort, '-')
                        ? $q->orderBy(Str::substr($request->sort, 1), 'desc')
                        : $q->orderBy($request->sort)
                )
                ->paginate($request->per_page)
        )->additional(['success' => true, 'sortables' => $sortables]);
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
            new OpenApiJsonResponse(resourceName: 'SingleUserResource'),
            new OpenApiNotFoundResponse,
        ]
    )]
    public function getSingle(int $id): JsonResponse
    {
        return ApiResponse::success(SingleUserResource::make(User::findOrFail($id)));
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
            new OpenApiJsonResponse(resourceName: 'SingleUserResource'),
        ]
    )]
    public function create(UserCreateRequest $request): JsonResponse
    {
        $model = new User;
        $model->fill($request->validated());
        $model->password = bcrypt($request->password);

        if ($model->save()) {
            return ApiResponse::success(SingleUserResource::make($model));
        }

        return ApiResponse::error('Error on creating user');
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
            new OpenApiJsonResponse(resourceName: 'SingleUserResource'),
            new OpenApiNotFoundResponse,
        ]
    )]
    public function update(int $id, UserUpdateRequest $request): JsonResponse
    {
        $user = User::findOrFail($id);
        $this->userService->updateDetails($user, $request);

        return ApiResponse::success(SingleUserResource::make($user));
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
            new OpenApiSingleDataResponse(data: 'true', type: 'boolean'),
        ],
    )]
    public function changePassword(int $id, PasswordRequest $request)
    {
        $user = User::findOrFail($id);
        if ($this->userService->updatePassword($user, $request)) {
            return ApiResponse::success(SingleUserResource::make($user));
        }

        return ApiResponse::error('Password not updated');
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
            new OpenApiJsonResponse(resourceName: 'SingleUserResource'),
        ],
    )]
    public function changeTheme(int $id, UserThemeRequest $request)
    {
        $user = User::findOrFail($id);
        if ($this->userService->updateTheme($user, $request)) {
            return ApiResponse::success(SingleUserResource::make($user));
        }

        return ApiResponse::error('Theme not updated');
    }

    #[OA\Delete(
        path: '/api/v1/users/{id}',
        description: 'Users delete route',
        tags: ['Users'],
        parameters: [
            new OpenApiIdParameter(modelName: 'User'),
        ],
        responses: [
            new OpenApiSingleDataResponse(data: 'true', type: 'boolean'),
        ]
    )]
    public function delete(int $id): JsonResponse
    {
        User::findOrFail($id)->deleteOrFail();

        return ApiResponse::success(true);
    }

    #[OA\Get(
        path: '/api/v1/users/{id}/devices',
        description: 'My devices route',
        tags: ['Users'],
        parameters: [
            new OpenApiIdParameter(modelName: 'User'),
        ],
        responses: [
            new OpenApiJsonArrayResponse(resourceName: 'DeviceInfoResource'),
        ]
    )]
    public function devices(int $id): JsonResponse
    {
        $user = User::findOrFail($id);

        return ApiResponse::success(DeviceInfoResource::collection($user->tokens));
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
            new OpenApiSingleDataResponse(data: 'true', type: 'boolean'),
            new OpenApiNotFoundResponse,
        ]
    )]
    public function revokeDevice(int $id): JsonResponse
    {
        return ApiResponse::success(PersonalAccessToken::findOrFail($id)->delete());
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
            new OpenApiJsonResponse(resourceName: 'SingleUserResource'),
            new OpenApiNotFoundResponse,
        ]
    )]
    public function attachRole(int $id, AttachRoleRequest $request)
    {
        $user = User::findOrFail($id);
        if ($user->update(['role_id' => $request->role_id])) {
            return ApiResponse::success(SingleUserResource::make($user));
        }

        return ApiResponse::error('Role not attached');
    }
}
