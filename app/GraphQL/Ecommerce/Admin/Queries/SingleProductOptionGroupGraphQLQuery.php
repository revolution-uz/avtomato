<?php

namespace App\GraphQL\Ecommerce\Admin\Queries;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Ecommerce\Admin\Types\ProductOptionGroupGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\Ecommerce\ProductOptionGroup;
use Closure;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class SingleProductOptionGroupGraphQLQuery extends Query
{
    protected $attributes = [
        'name' => 'singleProductOptionGroup',
    ];

    public function type(): Type
    {
        return GraphQL::type(ProductOptionGroupGraphQLType::GRAPHQL_TYPE_NAME);
    }

    public function args(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    Rule::exists(ProductOptionGroup::class, 'id')
                        ->where(function ($query) {
                            $query->whereNull('deleted_at');
                        }),
                ],
            ],
        ];
    }

    /**
     * @throws Error
     */
    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::PRODUCT_OPTION_GROUP_VIEW);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return ProductOptionGroup::query()->findOrFail($args['id']);
    }
}
