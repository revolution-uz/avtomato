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
use App\Http\Requests\Rbac\RolePermissionRequest;
use App\Http\Requests\Rbac\RoleRequest;
use App\Http\Requests\Rbac\RolesDeleteRequest;
use App\Http\Requests\Rbac\RolesFilterRequest;
use App\Http\Resources\Rbac\RoleListResource;
use App\Http\Resources\Rbac\SingleRoleResource;
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
            new OpenApiPageParameter,
            new OpenApiPerPageParameter,
            new OpenApiSearchParameter,
            new OpenApiSortParameter,
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
                    fn (Builder $q) => $q
                        ->where('name', 'ILIKE', '%' . $request->search . '%')
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
        path: '/api/v1/roles/{id}',
        description: 'Get single role route',
        tags: ['Roles'],
        parameters: [
            new OpenApiIdParameter(modelName: 'Role'),
        ],
        responses: [
            new OpenApiJsonResponse(resourceName: 'SingleRoleResource'),
            new OpenApiNotFoundResponse,
        ]
    )]
    public function getSingle(int $id): JsonResponse
    {
        return ApiResponse::success(SingleRoleResource::make(Role::findOrFail($id)));
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
        $model = new Role;
        $model->fill($request->validated());

        if ($model->save()) {
            return ApiResponse::success(RoleListResource::make($model));
        }

        return ApiResponse::error('Error on creating a role');
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
            new OpenApiNotFoundResponse,
        ]
    )]
    public function update(int $id, RoleRequest $request): JsonResponse
    {
        $role = Role::findOrFail($id);
        $role->fill($request->validated());

        if ($role->save()) {

            return ApiResponse::success(RoleListResource::make($role));
        }

        return ApiResponse::error('Error on updating a role');
    }

    #[OA\Post(
        path: '/api/v1/roles/{id}/attach-permissions',
        description: 'Attach permissions to role route',
        requestBody: new OA\RequestBody(
            description: 'Attach permissions to role request body',
            required: true,
            content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/RolePermissionRequest'))
        ),
        tags: ['Roles'],
        parameters: [
            new OpenApiIdParameter(modelName: 'Role'),
        ],
        responses: [
            new OpenApiJsonResponse(resourceName: 'SingleRoleResource'),
            new OpenApiNotFoundResponse,
        ]
    )]
    public function attachPermissions(int $id, RolePermissionRequest $request)
    {
        $role = Role::findOrFail($id);

        $filtered = array_filter($request->validated(), fn ($item) => $item['enabled'] !== false);

        // additional validation for permissions
        $mapped = array_reduce(
            $filtered,
            static function ($carry, $item) {
                $carry[$item['permission_id']][] = $item['branch_id'];

                return $carry;
            },
            []
        );
        foreach ($mapped as $item) {
            if (in_array(null, $item, true) && count($item) > 1) {
                return ApiResponse::error('You can\'t attach permission to all branches and specific branch at the same time');
            }
        }

        $role->syncPermissions($filtered);

        return ApiResponse::success(SingleRoleResource::make($role));
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
}
