<?php

namespace App\GraphQL\Management\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Enums\ModuleEnum;
use App\GraphQL\Management\Types\AdminGraphQLType;
use App\GraphQL\Management\Types\AdminInputGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\CompanyAdmin;
use App\Models\Company\Module;
use Closure;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class CreateAdminGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'createAdmin',
    ];

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(AdminGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function args(): array
    {
        return [
            'admin' => [
                'name' => 'admin',
                'type' => Type::nonNull(GraphQL::type(AdminInputGraphQLType::GRAPHQL_TYPE_NAME)),
            ],
        ];
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::ADMIN_CREATE);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $module = Module::query()->where('name', 'ILIKE', 'multibranch_%')
            ->where('is_active', true)
            ->first();
        $adminsCount = CompanyAdmin::count();
        $adminsLimit = ModuleEnum::getAdminsLimit($module->name);
        if ($module && $adminsLimit <= $adminsCount) {
            return new Error(__('api.n_admins', ['n' => $adminsLimit]));
        }
        $model = new CompanyAdmin;
        $model->fill($args['admin']);
        $model->password = bcrypt($args['admin']['password']);
        $model->saveOrFail();

        return $model;
    }

    protected function rules(array $args = []): array
    {
        return array_merge(parent::rules($args), [
            'admin.username' => Rule::unique('admins', 'username'),
        ]);
    }
}
