<?php

namespace App\GraphQL\EcommerceClient;

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
use App\GraphQL\EcommerceClient\Mutation\AttachProductToCartMutation;
use App\GraphQL\EcommerceClient\Mutation\AttachProductToFavoriteMutation;
use App\GraphQL\EcommerceClient\Mutation\CreateOrderItemMutation;
use App\GraphQL\EcommerceClient\Mutation\CreateOrderMutation;
use App\GraphQL\EcommerceClient\Queries\OrdersGraphQLQuery;
use App\GraphQL\EcommerceClient\Queries\UserCartGraphQLQuery;
use App\GraphQL\EcommerceClient\Queries\UserFavoriteGraphQLQuery;
use App\GraphQL\Management\Types\AttachmentGraphQLType;
use Rebing\GraphQL\Support\Contracts\ConfigConvertible;

class EcommerceClientGraphQLSchema implements ConfigConvertible
{
    public function toConfig(): array
    {
        return [
            'query' => [
                OrdersGraphQLQuery::class,
                UserCartGraphQLQuery::class,
                UserFavoriteGraphQLQuery::class,
            ],
            'mutation' => [
                AttachProductToCartMutation::class,
                AttachProductToFavoriteMutation::class,
                CreateOrderItemMutation::class,
                CreateOrderMutation::class,
            ],
            'types' => [
                EcommerceImageGraphQLType::class,
                AttachmentGraphQLType::class,
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
            ],
            'middleware' => ['auth:sanctum', 'is_soft_delete_client_token'],
        ];
    }
}
