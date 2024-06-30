<?php

namespace App\GraphQL\Management\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Management\Types\AdminGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\User;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class UpdateAdminGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'updateAdmin',
    ];

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(AdminGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function args(): array
    {
        return [
            'id' => [
                'name' => 'id',
                'type' => Type::nonNull(Type::int()),
            ],
            'first_name' => [
                'name' => 'first_name',
                'type' => Type::string(),
            ],
            'last_name' => [
                'name' => 'last_name',
                'type' => Type::string(),
            ],
        ];
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::ADMIN_EDIT);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $model = User::findOrFail($args['id']);
        $model->updateOrFail($args);
        $model->saveOrFail();

        return $model;
    }

    protected function rules(array $args = []): array
    {
        return [
            'id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')
                    ->where('id', $args['id']),
            ],
            'first_name' => [
                'nullable',
                'first_name',
                'max:50',
            ],
            'last_name' => [
                'nullable',
                'last_name',
                'max:50',
            ],
        ];
    }
}
