<?php

namespace App\GraphQL\Ecommerce\Admin\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Ecommerce\Admin\Types\ProductOptionGroupGraphQLType;
use App\GraphQL\Ecommerce\Admin\Types\ProductOptionGroupInputGraphQLType;
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

class CreateProductOptionGroupGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'createProductOptionGroup',
    ];

    public function args(): array
    {
        return [
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
        return RbacHelper::checkPermission(PermissionEnum::PRODUCT_OPTION_GROUP_CREATE);
    }

    /**
     * @throws Throwable
     */
    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields): ProductOptionGroup
    {

        $model = new ProductOptionGroup;
        $args['productOptionGroup']['slug'] = Str::slug($args['productOptionGroup']['name_en']);
        $model->fill($args['productOptionGroup']);
        $model->saveOrFail();

        return $model;
    }
}
