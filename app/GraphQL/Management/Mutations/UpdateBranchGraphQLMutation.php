<?php

namespace App\GraphQL\Management\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Management\Types\BranchGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\Branch;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class UpdateBranchGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'updateBranch',
    ];

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(BranchGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function args(): array
    {
        return [
            'id' => [
                'name' => 'id',
                'type' => Type::nonNull(Type::int()),
            ],
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
        return RbacHelper::checkPermission(PermissionEnum::BRANCH_EDIT);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $model = Branch::findOrFail($args['id']);
        $model->fill($args);
        $model->saveOrFail();

        return $model;
    }

    protected function rules(array $args = []): array
    {
        return [
            'id' => [
                'required',
                'integer',
                Rule::exists('branches', 'id')
                    ->where('id', $args['id']),
            ],
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
