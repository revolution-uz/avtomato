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
use App\Http\Requests\User\RoleRequest;
use App\Http\Requests\User\RolesDeleteRequest;
use App\Http\Requests\User\RolesFilterRequest;
use App\Http\Resources\PermissionGroupResource;
use App\Http\Resources\User\RoleListResource;
use App\Http\Resources\User\SingleRoleResource;
use App\Models\Company\Rbac\PermissionGroup;
use App\Models\Company\Rbac\Role;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use OpenApi\Attributes as OA;

class RoleController extends Controller
{
    #[OA\Get(
        path: '/api/v1/roles',
        description: 'Get roles list route',
        tags: ['Roles'],
        parameters: [
            new OpenApiPageParameter(),
            new OpenApiPerPageParameter(),
            new OpenApiSearchParameter(),
            new OpenApiSortParameter(),
        ],
        responses: [
            new OpenApiJsonArrayResponse(resourceName: 'RoleListResource', withPagination: true, withSortables: true),
        ]
    )]
    public function index(RolesFilterRequest $request)
    {
        $sortables = ['id', 'name', 'created_at', 'updated_at'];

        return RoleListResource::collection(
            Role::query()
                ->when(
                    $request->search,
                    fn(Builder $q) => $q
                        ->where('name', 'ILIKE', '%' . $request->search . '%')
                )
                ->when(
                    $request->sort && in_array($request->sort, $sortables),
                    fn(Builder $q) => Str::startsWith($request->sort, '-')
                        ? $q->orderBy(Str::substr($request->sort, 1), 'desc')
                        : $q->orderBy($request->sort)
                )
                ->paginate($request->per_page)
        )->additional(['success' => true, 'sortables' => $sortables]);
    }

    #[OA\Get(
        path: '/api/v1/roles/{id}',
        description: 'Get single role route',
        tags: ['Roles'],
        parameters: [
            new OpenApiIdParameter(modelName: 'Role'),
        ],
        responses: [
            new OpenApiJsonResponse(resourceName: 'SingleRoleResource'),
            new OpenApiNotFoundResponse(),
        ]
    )]
    public function getSingle(Role $role): JsonResponse
    {
        return ApiResponse::success(SingleRoleResource::make($role));
    }

    #[OA\Post(
        path: '/api/v1/roles',
        description: 'Create role route',
        requestBody: new OA\RequestBody(
            description: 'Create role request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/RoleRequest')
        ),
        tags: ['Roles'],
        responses: [
            new OpenApiJsonResponse(resourceName: 'SingleRoleResource'),
        ]
    )]
    public function create(RoleRequest $request): JsonResponse
    {
        $model = new Role();
        $model->fill($request->validated() + ['guard_name' => 'web']);

        if ($model->save()) {
            $model->syncPermissions($request->permissions);
            return ApiResponse::success(SingleRoleResource::make($model));
        }

        return ApiResponse::error("Error on creating a role");
    }

    #[OA\Put(
        path: '/api/v1/roles/{id}',
        description: 'Create a role route',
        requestBody: new OA\RequestBody(
            description: 'Update role request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/RoleRequest')
        ),
        tags: ['Roles'],
        parameters: [
            new OpenApiIdParameter(modelName: 'Role'),
        ],
        responses: [
            new OpenApiJsonResponse(resourceName: 'SingleRoleResource'),
            new OpenApiNotFoundResponse(),
        ]
    )]
    public function update(Role $role, RoleRequest $request): JsonResponse
    {
        $role->fill($request->validated());

        if ($role->save()) {
            $role->syncPermissions($request->permissions);
            return ApiResponse::success(SingleRoleResource::make($role));
        }

        return ApiResponse::error("Error on updating a role");
    }

    #[OA\Delete(
        path: '/api/v1/roles',
        description: 'Delete role route',
        requestBody: new OA\RequestBody(
            description: 'Roles delete request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/RolesDeleteRequest')
        ),
        tags: ['Roles'],
        responses: [
            new OpenApiSingleDataResponse(data: 'true', type: 'boolean'),
        ]
    )]
    public function delete(RolesDeleteRequest $request): JsonResponse
    {
        Role::query()->whereIn('id', $request->ids)->delete();

        return ApiResponse::success(true);
    }

    #[OA\Get(
        path: '/api/v1/roles/permissions',
        description: 'Get permissions list route',
        tags: ['Roles'],
        responses: [
            new OpenApiSingleDataResponse(data: 'true', type: 'boolean'),
        ]
    )]
    public function permissions()
    {
        return ApiResponse::success(PermissionGroupResource::collection(PermissionGroup::query()->get()));
    }
}
