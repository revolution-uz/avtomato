<?php

namespace App\GraphQL\Ecommerce\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Ecommerce\Types\ProductOptionGroupGraphQLType;
use App\GraphQL\Ecommerce\Types\ProductOptionGroupInputGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\Ecommerce\ProductOptionGroup;
use Closure;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Str;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use Throwable;

class UpdateProductOptionGroupGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'updateProductOptionGroup',
    ];

    public function args(): array
    {
        return [
            'id' => [
                'name' => 'id',
                'type' => Type::nonNull(Type::int()),
                'rules' => ['exists:ecommerce_product_option_groups,id'],
            ],
            'productOptionGroup' => [
                'name' => 'productOptionGroup',
                'type' => Type::nonNull(GraphQL::type(ProductOptionGroupInputGraphQLType::GRAPHQL_TYPE_NAME)),
            ],
        ];
    }

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(ProductOptionGroupGraphQLType::GRAPHQL_TYPE_NAME));
    }

    /**
     * @throws Error
     */
    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::PRODUCT_OPTION_GROUP_EDIT);
    }

    /**
     * @throws Throwable
     */
    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields): ProductOptionGroup
    {
        $model = ProductOptionGroup::query()->findOrFail($args['id']);
        $args['productOptionGroup']['slug'] = Str::slug($args['productOptionGroup']['name_en']);
        $model->update($args['productOptionGroup']);
        $model->refresh();

        return $model;
    }
}
