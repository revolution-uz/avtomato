<?php

namespace App\GraphQL\EcommerceClient;

use App\GraphQL\Ecommerce\Queries\ProductBrandsGraphQLQuery;
use App\GraphQL\Ecommerce\Queries\ProductCategoriesGraphQLQuery;
use App\GraphQL\Ecommerce\Queries\ProductsGraphQLQuery;
use App\GraphQL\Ecommerce\Types\BannerGraphQLType;
use App\GraphQL\Ecommerce\Types\OrderInputGraphQLType;
use App\GraphQL\Ecommerce\Types\ProductBrandGraphQLType;
use App\GraphQL\Ecommerce\Types\ProductCategoryGraphQLType;
use App\GraphQL\Ecommerce\Types\ProductGraphQLType;
use App\GraphQL\Ecommerce\Types\ProductJoinOptionGraphQLType;
use App\GraphQL\Ecommerce\Types\ProductOptionGraphQLType;
use App\GraphQL\Ecommerce\Types\ProductOptionValueGraphQLType;
use App\GraphQL\Ecommerce\Types\UserCartInputGraphQLType;
use App\GraphQL\Ecommerce\Types\UserFavoriteInputGraphQLType;
use App\GraphQL\EcommerceClient\Mutation\AttachProductToCartMutation;
use App\GraphQL\EcommerceClient\Mutation\AttachProductToFavoriteMutation;
use App\GraphQL\EcommerceClient\Mutation\CreateOrderItemMutation;
use App\GraphQL\EcommerceClient\Mutation\CreateOrderMutation;
use App\GraphQL\EcommerceClient\Mutation\LoginGraphQLMutation;
use App\GraphQL\EcommerceClient\Mutation\RegisterGraphQLMutation;
use App\GraphQL\EcommerceClient\Queries\SingleProductBrandGraphQLQuery;
use App\GraphQL\EcommerceClient\Queries\SingleProductCategoryGraphQLQuery;
use App\GraphQL\EcommerceClient\Queries\SingleProductGraphQLQuery;
use App\GraphQL\EcommerceClient\Queries\UserCartGraphQLQuery;
use Rebing\GraphQL\Support\Contracts\ConfigConvertible;

class EcommerceClientGraphQLSchema implements ConfigConvertible
{
    public function toConfig(): array
    {
        return [
            'query' => [
                ProductCategoriesGraphQLQuery::class,
                ProductsGraphQLQuery::class,
                ProductBrandsGraphQLQuery::class,
                SingleProductGraphQLQuery::class,
                SingleProductCategoryGraphQLQuery::class,
                SingleProductBrandGraphQLQuery::class,
                UserCartGraphQLQuery::class,
            ],
            'mutation' => [
                AttachProductToCartMutation::class,
                AttachProductToFavoriteMutation::class,
                CreateOrderItemMutation::class,
                CreateOrderMutation::class,
                RegisterGraphQLMutation::class,
                LoginGraphQLMutation::class,
            ],
            'types' => [
                ProductCategoryGraphQLType::class,
                ProductGraphQLType::class,
                BannerGraphQLType::class,
                ProductBrandGraphQLType::class,
                OrderInputGraphQLType::class,
                ProductJoinOptionGraphQLType::class,
                ProductOptionValueGraphQLType::class,
                ProductOptionGraphQLType::class,
                UserCartInputGraphQLType::class,
                UserFavoriteInputGraphQLType::class,
            ],
            'middleware' => ['optional_auth:sanctum'],
        ];
    }
}
