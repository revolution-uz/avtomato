<?php

namespace App\GraphQL\Market\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Market\Types\ProductCategoryGraphQLType;
use App\GraphQL\Market\Types\ProductCategoryInputGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\ProductCategory;
use Closure;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use Throwable;

class CreateProductCategoryGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'createProductCategory',
    ];

    public function args(): array
    {
        return [
            'productCategory' => [
                'name' => 'productCategory',
                'type' => Type::nonNull(GraphQL::type(ProductCategoryInputGraphQLType::GRAPHQL_TYPE_NAME)),
            ],
        ];
    }

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(ProductCategoryGraphQLType::GRAPHQL_TYPE_NAME));
    }

    /**
     * @throws Error
     */
    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::PRODUCT_CATEGORY_CREATE);
    }

    /**
     * @throws Throwable
     */
    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields): ProductCategory
    {

        $model = new ProductCategory;
        $model->fill($args['productCategory']);
        $model->saveOrFail();

        return $model;
    }

    protected function rules(array $args = []): array
    {
        return parent::rules($args);
    }
}
