<?php

namespace App\GraphQL\Ecommerce\Admin\Queries;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Ecommerce\Admin\Types\ProductOptionGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\Ecommerce\ProductOption;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class SingleProductOptionGraphQLQuery extends Query
{
    protected $attributes = [
        'name' => 'singleProductOption',
    ];

    public function type(): Type
    {
        return GraphQL::type(ProductOptionGraphQLType::GRAPHQL_TYPE_NAME);
    }

    public function args(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    Rule::exists(ProductOption::class, 'id')->whereNull('deleted_at'),
                ],
            ],
        ];
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::PRODUCT_OPTION_VALUE_VIEW);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return ProductOption::query()->findOrFail($args['id']);
    }
}
