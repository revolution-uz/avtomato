<?php

namespace App\GraphQL\Ecommerce;

use App\GraphQL\Ecommerce\Mutations\CreateBannerGraphQLMutation;
use App\GraphQL\Ecommerce\Mutations\CreateProductBrandGraphQLMutation;
use App\GraphQL\Ecommerce\Mutations\CreateProductCategoryGraphQLMutation;
use App\GraphQL\Ecommerce\Mutations\CreateProductGraphQLMutation;
use App\GraphQL\Ecommerce\Mutations\CreateProductOptionValueGraphQLMutation;
use App\GraphQL\Ecommerce\Queries\ProductBrandsGraphQLQuery;
use App\GraphQL\Ecommerce\Queries\ProductCategoriesGraphQLQuery;
use App\GraphQL\Ecommerce\Queries\ProductsGraphQLQuery;
use App\GraphQL\Ecommerce\Types\BannerGraphQLType;
use App\GraphQL\Ecommerce\Types\BannerInputGraphQLType;
use App\GraphQL\Ecommerce\Types\OrderInputGraphQLType;
use App\GraphQL\Ecommerce\Types\OrderItemInputGraphQLType;
use App\GraphQL\Ecommerce\Types\OrderStatusInputGraphQLType;
use App\GraphQL\Ecommerce\Types\ProductBrandGraphQLType;
use App\GraphQL\Ecommerce\Types\ProductBrandInputGraphQLType;
use App\GraphQL\Ecommerce\Types\ProductCategoryGraphQLType;
use App\GraphQL\Ecommerce\Types\ProductCategoryInputGraphQLType;
use App\GraphQL\Ecommerce\Types\ProductGraphQLType;
use App\GraphQL\Ecommerce\Types\ProductInputGraphQLType;
use App\GraphQL\Ecommerce\Types\ProductJoinOptionGraphQLType;
use App\GraphQL\Ecommerce\Types\ProductJoinOptionInputGraphQLType;
use App\GraphQL\Ecommerce\Types\ProductOptionGraphQLType;
use App\GraphQL\Ecommerce\Types\ProductOptionInputGraphQLType;
use App\GraphQL\Ecommerce\Types\ProductOptionValueGraphQLType;
use App\GraphQL\Ecommerce\Types\ProductOptionValueInputGraphQLType;
use App\GraphQL\Ecommerce\Types\ProductVariationInputGraphQLType;
use App\GraphQL\Ecommerce\Types\UserCartInputGraphQLType;
use App\GraphQL\Ecommerce\Types\UserFavoriteInputGraphQLType;
use Rebing\GraphQL\Support\Contracts\ConfigConvertible;

class EcommerceGraphQLSchema implements ConfigConvertible
{
    public function toConfig(): array
    {
        return [
            'mutation' => [
                CreateProductCategoryGraphQLMutation::class,
                CreateProductGraphQLMutation::class,
                CreateBannerGraphQLMutation::class,
                CreateProductBrandGraphQLMutation::class,
                CreateProductOptionValueGraphQLMutation::class,
            ],
            'query' => [
                ProductCategoriesGraphQLQuery::class,
                ProductsGraphQLQuery::class,
                ProductBrandsGraphQLQuery::class,
            ],
            'types' => [
                ProductCategoryGraphQLType::class,
                ProductCategoryInputGraphQLType::class,
                ProductGraphQLType::class,
                BannerGraphQLType::class,
                BannerInputGraphQLType::class,
                ProductInputGraphQLType::class,
                ProductBrandGraphQLType::class,
                ProductBrandInputGraphQLType::class,
                OrderInputGraphQLType::class,
                OrderItemInputGraphQLType::class,
                OrderStatusInputGraphQLType::class,
                ProductJoinOptionGraphQLType::class,
                ProductJoinOptionInputGraphQLType::class,
                ProductOptionValueGraphQLType::class,
                ProductOptionValueInputGraphQLType::class,
                ProductOptionGraphQLType::class,
                ProductOptionInputGraphQLType::class,
                ProductVariationInputGraphQLType::class,
                UserCartInputGraphQLType::class,
                UserFavoriteInputGraphQLType::class,
            ],
            'middleware' => ['auth:sanctum', 'is_soft_delete_token', 'tenant_logging'],
        ];
    }
}
