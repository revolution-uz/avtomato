<?php

namespace App\GraphQL\Ecommerce\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Ecommerce\Types\ProductOptionValueGraphQLType;
use App\GraphQL\Ecommerce\Types\ProductOptionValueInputGraphQLType;
use App\Models\Company\Ecommerce\ProductOptionValue;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Str;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use Throwable;

class UpdateProductOptionValueGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'updateProductOptionValue',
    ];

    public function args(): array
    {
        return [
            'id' => [
                'name' => 'id',
                'type' => Type::nonNull(Type::int()),
                'rules' => ['exists:e_commerce_product_option_values,id'],
            ],
            'productOptionValue' => [
                'name' => 'productOptionValue',
                'type' => Type::nonNull(GraphQL::type(ProductOptionValueInputGraphQLType::GRAPHQL_TYPE_NAME)),
            ],
        ];
    }

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(ProductOptionValueGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return true;
        //            returnRbacHelper::checkPermission(PermissionEnum::PRODUCT_CATEGORY_UPDATE);
    }

    /**
     * @throws Throwable
     */
    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields): ProductOptionValue
    {
        $model = ProductOptionValue::query()->findOrFail($args['id']);
        $args['productOptionValue']['slug'] = Str::slug($args['productOptionValue']['value_en']);
        $model->update($args['productOptionValue']);
        $model->refresh();

        return $model;
    }
}
