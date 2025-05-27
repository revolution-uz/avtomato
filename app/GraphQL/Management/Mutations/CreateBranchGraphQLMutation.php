<?php

namespace App\GraphQL\Management\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Enums\ModuleEnum;
use App\GraphQL\Management\Types\BranchGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\Branch;
use App\Models\Company\Module;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Database\Eloquent\Model;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

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
                'rules' => [
                    'required',
                    'string',
                    'max:255',
                    function ($attribute, $value, $fail) {
                        if (! $module = Module::getActiveMultiBranchModule()) {
                            $fail('There is no active branch module');

                            return;
                        }
                        $branchLimit = ModuleEnum::getBranchLimit($module->name);
                        if ($branchLimit <= Branch::query()->count()) {
                            $fail(__('api.n_branches', ['n' => $branchLimit]));
                        }
                    },
                ],
            ],
            'address' => [
                'name' => 'address',
                'type' => Type::nonNull(Type::string()),
                'rules' => 'required|string|max:255',
            ],
            'inn' => [
                'name' => 'inn',
                'type' => Type::nonNull(Type::string()),
                'rules' => 'required|string|max:9',
            ],
        ];
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::BRANCH_CREATE);
    }

    public function resolve($root, array $args): Model|Branch
    {
        return Branch::query()->create($args);
    }
}
