<?php

namespace App\Http\Controllers\PartnerApi\V1\User;

use App\Enums\Company\Rbac\UserRoleEnum;
use App\Exceptions\ForbiddenException;
use App\Helpers\Swagger\OpenApiArrayResponse;
use App\Helpers\Swagger\OpenApiDataResponse;
use App\Helpers\Swagger\OpenApiIdParameter;
use App\Helpers\Swagger\OpenApiNotFoundResponse;
use App\Helpers\Swagger\OpenApiPageParameter;
use App\Helpers\Swagger\OpenApiPerPageParameter;
use App\Helpers\Swagger\OpenApiSearchParameter;
use App\Helpers\Swagger\OpenApiSortParameter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Rbac\RolePermissionRequest;
use App\Http\Requests\Rbac\RoleRequest;
use App\Http\Requests\Rbac\RolesDeleteRequest;
use App\Http\Requests\Rbac\RolesFilterRequest;
use App\Http\Requests\Rbac\RoleViewRequest;
use App\Http\Resources\Rbac\RoleListResource;
use App\Http\Resources\Rbac\SingleRoleResource;
use App\Http\Resources\StatusResource;
use App\Models\Company\Rbac\Role;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
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
            new OpenApiArrayResponse(resourceName: 'RoleListResource', withPagination: true, withSortables: true),
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
                ->paginate($request->per_page, page: $request->page)
        )->additional(['sortables' => $sortables]);
    }

    #[OA\Get(
        path: '/api/v1/roles/{id}',
        description: 'Get single role route',
        tags: ['Roles'],
        parameters: [
            new OpenApiIdParameter(modelName: 'Role'),
        ],
        responses: [
            new OpenApiDataResponse(resourceName: 'SingleRoleResource'),
            new OpenApiNotFoundResponse,
        ]
    )]
    public function getSingle(int $id, RoleViewRequest $request): SingleRoleResource
    {
        return new SingleRoleResource(Role::findOrFail($id));
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
            new OpenApiDataResponse(resourceName: 'SingleRoleResource', response: '201'),
        ]
    )]
    public function create(RoleRequest $request): RoleListResource
    {
        $model = new Role;
        $model->fill($request->validated());
        $model->saveOrFail();

        return new RoleListResource($model);
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
            new OpenApiDataResponse(resourceName: 'SingleRoleResource'),
            new OpenApiNotFoundResponse,
        ]
    )]
    public function update(int $id, RoleRequest $request): RoleListResource
    {
        /** @var Role $role */
        $role = Role::findOrFail($id);

        if ($role->name === UserRoleEnum::ADMIN->value) {
            throw new ForbiddenException('You can\'t update admin role');
        }

        $role->fill($request->validated());
        $role->saveOrFail();

        return new RoleListResource($role);
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
            new OpenApiDataResponse(resourceName: 'SingleRoleResource'),
            new OpenApiNotFoundResponse,
        ]
    )]
    public function attachPermissions(int $id, RolePermissionRequest $request)
    {
        /** @var Role $role */
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
                throw ValidationException::withMessages(['permission_id' => 'You can\'t attach permission to all branches and specific branch at the same time']);
            }
        }

        $role->syncPermissions($filtered);

        return new SingleRoleResource($role);
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
            new OpenApiDataResponse(resourceName: 'StatusResource'),
        ]
    )]
    public function delete(RolesDeleteRequest $request): StatusResource
    {
        Role::query()->whereIn('id', $request->ids)->delete();

        return new StatusResource(true);
    }
}
