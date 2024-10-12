<?php

namespace App\GraphQL\Management\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Enums\ModuleEnum;
use App\GraphQL\Management\Types\BranchGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\Branch;
use App\Models\Company\Module;
use Closure;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use Throwable;

class CreateBranchGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'createBranch',
    ];

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(BranchGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function args(): array
    {
        return [
            'name' => [
                'name' => 'name',
                'type' => Type::nonNull(Type::string()),
            ],
            'address' => [
                'name' => 'address',
                'type' => Type::nonNull(Type::string()),
            ],
            'inn' => [
                'name' => 'inn',
                'type' => Type::nonNull(Type::string()),
            ],
        ];
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::BRANCH_CREATE);
    }

    /**
     * @throws Throwable
     */
    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $module = Module::query()
            ->where('name', 'ILIKE', 'multibranch_%')
            ->where('is_active', true)
            ->first();
        $branchCount = Branch::query()->count();
        $branchLimit = ModuleEnum::getBranchLimit($module->name);
        if ($module && $branchLimit <= $branchCount) {
            return new Error(__('api.n_branches', ['n' => $branchLimit]));
        }
        $model = new Branch;
        $model->fill($args);
        $model->saveOrFail();

        return $model;
    }

    protected function rules(array $args = []): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'address' => [
                'required',
                'string',
                'max:255',
            ],
            'inn' => [
                'required',
                'string',
                'max:9',
            ],
        ];
    }
}
