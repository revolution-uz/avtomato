<?php

namespace App\GraphQL\Management\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Management\Types\AdminGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\CompanyAdmin;
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
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    'integer',
                    Rule::exists(CompanyAdmin::class, 'id')
                        ->where(function ($query) {
                            $query->where('is_system', false)
                                ->whereNull('deleted_at');
                        }),
                ],
            ],
            'first_name' => [
                'type' => Type::string(),
                'rules' => ['nullable', 'string', 'max:50'],
            ],
            'last_name' => [
                'type' => Type::string(),
                'rules' => ['nullable', 'string', 'max:50'],
            ],
        ];
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::ADMIN_EDIT);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $model = CompanyAdmin::findOrFail($args['id']);
        $model->updateOrFail($args);

        return $model;
    }
}
