<?php

namespace App\GraphQL\Ecommerce\Mutations;

use App\GraphQL\Ecommerce\Types\ProductOptionValueGraphQLType;
use App\GraphQL\Ecommerce\Types\ProductOptionValueInputGraphQLType;
use App\Models\Company\Ecommerce\Product;
use App\Models\Company\Ecommerce\ProductOptionValue;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use Throwable;

class CreateProductOptionValueGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'createProductOptionValue',
    ];

    public function args(): array
    {
        return [
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
        //        return RbacHelper::checkPermission(PermissionEnum::PRODUCT_CATEGORY_CREATE);
    }

    /**
     * @throws Throwable
     */
    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields): ProductOptionValue
    {

        $model = new ProductOptionValue;
        $model->fill($args['productOptionValue']);
        $model->saveOrFail();

        return $model;
    }

    protected function rules(array $args = []): array
    {
        return parent::rules($args);
    }
}
