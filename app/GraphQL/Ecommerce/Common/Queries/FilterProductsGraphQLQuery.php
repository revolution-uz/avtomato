<?php

namespace App\GraphQL\Ecommerce\Common\Queries;

use App\DTOs\Ecommerce\Base\BaseEcommerceFilterRequestDTO;
use App\DTOs\Ecommerce\Product\EcommerceProductFilterRequestDTO;
use App\GraphQL\Ecommerce\Admin\Types\ProductGraphQLType;
use App\GraphQL\Ecommerce\Common\Types\ProductFilterInputGraphQLType;
use App\Services\Ecommerce\ProductService;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class FilterProductsGraphQLQuery extends Query
{
    protected $attributes = [
        'name' => 'filterProducts',
    ];

    public function __construct(readonly ProductService $productService) {}

    public function type(): Type
    {
        return GraphQL::paginate(ProductGraphQLType::GRAPHQL_TYPE_NAME);
    }

    public function args(): array
    {
        return [
            'sort' => [
                'type' => Type::string(),
                'rules' => 'nullable|string|regex:/^[-]?[a-z_]+$/',
            ],
            'search' => [
                'type' => Type::string(),
                'rules' => 'nullable|string|max:255',
            ],
            'page' => [
                'type' => Type::int(),
                'rules' => 'nullable|int|min:1',
            ],
            'per_page' => [
                'type' => Type::int(),
                'rules' => 'nullable|int|min:5|max:100',
            ],
            'filter' => [
                'type' => GraphQL::type(ProductFilterInputGraphQLType::GRAPHQL_TYPE_NAME),
            ],
        ];
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return true;
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return $this->productService->filtering(BaseEcommerceFilterRequestDTO::from($args), EcommerceProductFilterRequestDTO::from($args['filter'] ?? []));
    }
}
