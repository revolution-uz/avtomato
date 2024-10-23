<?php

namespace App\GraphQL\Market\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Market\Types\ProductGraphQLType;
use App\GraphQL\Market\Types\ProductInputGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\Product;
use Closure;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use Throwable;

class CreateProductGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'createProduct',
    ];

    public function args(): array
    {
        return [
            'product' => [
                'name' => 'product',
                'type' => Type::nonNull(GraphQL::type(ProductInputGraphQLType::GRAPHQL_TYPE_NAME)),
            ],
        ];
    }

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(ProductGraphQLType::GRAPHQL_TYPE_NAME));
    }

    /**
     * @throws Error
     */
    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return true;
        //        return RbacHelper::checkPermission(PermissionEnum::PRODUCT_CATEGORY_CREATE);
    }

    /**
     * @throws Throwable
     */
    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields): Product
    {

        $model = new Product;
        $model->fill($args['product']);
        $model->saveOrFail();

        return $model;
    }

    protected function rules(array $args = []): array
    {
        return parent::rules($args);
    }
}
