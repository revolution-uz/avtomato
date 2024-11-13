<?php

namespace App\GraphQL\EcommerceClient;

use App\GraphQL\Customer\Types\CustomerInputGraphQLType;
use App\GraphQL\Ecommerce\Types\BannerGraphQLType;
use App\GraphQL\Ecommerce\Types\EcommerceImageGraphQLType;
use App\GraphQL\Ecommerce\Types\OrderGraphQLType;
use App\GraphQL\Ecommerce\Types\OrderInputGraphQLType;
use App\GraphQL\Ecommerce\Types\OrderItemGraphQLType;
use App\GraphQL\Ecommerce\Types\ProductBrandGraphQLType;
use App\GraphQL\Ecommerce\Types\ProductCategoryGraphQLType;
use App\GraphQL\Ecommerce\Types\ProductGraphQLType;
use App\GraphQL\Ecommerce\Types\ProductJoinOptionGraphQLType;
use App\GraphQL\Ecommerce\Types\ProductOptionGraphQLType;
use App\GraphQL\Ecommerce\Types\ProductOptionValueGraphQLType;
use App\GraphQL\Ecommerce\Types\UserCartInputGraphQLType;
use App\GraphQL\Ecommerce\Types\UserFavoriteInputGraphQLType;
use App\GraphQL\Ecommerce\Types\UserGraphQLType;
use App\GraphQL\EcommerceClient\Common\Queries\BannersGraphQLQuery;
use App\GraphQL\EcommerceClient\Common\Queries\ProductBrandsGraphQLQuery;
use App\GraphQL\EcommerceClient\Common\Queries\ProductCategoriesGraphQLQuery;
use App\GraphQL\EcommerceClient\Common\Queries\ProductsGraphQLQuery;
use App\GraphQL\EcommerceClient\Common\Queries\SingleBannerGraphQLQuery;
use App\GraphQL\EcommerceClient\Common\Queries\SingleProductBrandGraphQLQuery;
use App\GraphQL\EcommerceClient\Common\Queries\SingleProductCategoryGraphQLQuery;
use App\GraphQL\EcommerceClient\Common\Queries\SingleProductGraphQLQuery;
use App\GraphQL\EcommerceClient\Mutation\AttachProductToCartGraphQLMutation;
use App\GraphQL\EcommerceClient\Mutation\AttachProductToFavoriteGraphQLMutation;
use App\GraphQL\EcommerceClient\Mutation\CreateOrderGraphQLMutation;
use App\GraphQL\EcommerceClient\Mutation\FillProfileGraphQLMutation;
use App\GraphQL\EcommerceClient\Queries\OrdersGraphQLQuery;
use App\GraphQL\EcommerceClient\Queries\UserCartGraphQLQuery;
use App\GraphQL\EcommerceClient\Queries\UserFavoriteGraphQLQuery;
use Rebing\GraphQL\Support\Contracts\ConfigConvertible;

class EcommerceClientGraphQLSchema implements ConfigConvertible
{
    public function toConfig(): array
    {
        return [
            'query' => [
                BannersGraphQLQuery::class,
                ProductCategoriesGraphQLQuery::class,
                ProductsGraphQLQuery::class,
                ProductBrandsGraphQLQuery::class,
                SingleBannerGraphQLQuery::class,
                SingleProductGraphQLQuery::class,
                SingleProductCategoryGraphQLQuery::class,
                SingleProductBrandGraphQLQuery::class,
                OrdersGraphQLQuery::class,
                UserCartGraphQLQuery::class,
                UserFavoriteGraphQLQuery::class,
            ],
            'mutation' => [
                AttachProductToCartGraphQLMutation::class,
                AttachProductToFavoriteGraphQLMutation::class,
                CreateOrderGraphQLMutation::class,
                FillProfileGraphQLMutation::class,
            ],
            'types' => [
                EcommerceImageGraphQLType::class,
                ProductCategoryGraphQLType::class,
                ProductGraphQLType::class,
                BannerGraphQLType::class,
                ProductBrandGraphQLType::class,
                OrderGraphQLType::class,
                OrderItemGraphQLType::class,
                OrderInputGraphQLType::class,
                ProductJoinOptionGraphQLType::class,
                ProductOptionValueGraphQLType::class,
                ProductOptionGraphQLType::class,
                UserCartInputGraphQLType::class,
                UserFavoriteInputGraphQLType::class,
                CustomerInputGraphQLType::class,
                UserGraphQLType::class,
            ],
            'middleware' => ['auth:sanctum', 'is_soft_delete_client_token'],
        ];
    }
}
