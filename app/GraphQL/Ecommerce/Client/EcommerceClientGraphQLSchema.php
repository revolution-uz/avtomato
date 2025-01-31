<?php

namespace App\GraphQL\Ecommerce\Client;

use Rebing\GraphQL\Support\Contracts\ConfigConvertible;

class EcommerceClientGraphQLSchema implements ConfigConvertible
{
    public function toConfig(): array
    {
        return [
            'query' => [
                \App\GraphQL\Ecommerce\Client\Queries\OrdersGraphQLQuery::class,
                \App\GraphQL\Ecommerce\Client\Queries\UserCartsGraphQLQuery::class,
                \App\GraphQL\Ecommerce\Client\Queries\UserFavouritesGraphQLQuery::class,
                \App\GraphQL\Ecommerce\Client\Queries\AddressesGraphQLQuery::class,
            ],
            'mutation' => [
                \App\GraphQL\Ecommerce\Client\Mutations\AddCartGraphQLMutation::class,
                \App\GraphQL\Ecommerce\Client\Mutations\AddFavouriteGraphQLMutation::class,
                \App\GraphQL\Ecommerce\Client\Mutations\RemoveCartGraphQLMutation::class,
                \App\GraphQL\Ecommerce\Client\Mutations\RemoveFavouriteGraphQLMutation::class,
                \App\GraphQL\Ecommerce\Client\Mutations\CreateOrderGraphQLMutation::class,
                \App\GraphQL\Ecommerce\Client\Mutations\CreateCustomerGraphQLMutation::class,
                \App\GraphQL\Ecommerce\Client\Mutations\AttachAddressGraphQLMutation::class,
                \App\GraphQL\Ecommerce\Client\Mutations\DetachAddressGraphQLMutation::class,
            ],
            'types' => [
                \App\GraphQL\Ecommerce\Admin\Types\EcommerceImageGraphQLType::class,
                \App\GraphQL\Ecommerce\Admin\Types\ProductCategoryGraphQLType::class,
                \App\GraphQL\Ecommerce\Admin\Types\ProductGraphQLType::class,
                \App\GraphQL\Ecommerce\Admin\Types\BannerGraphQLType::class,
                \App\GraphQL\Ecommerce\Admin\Types\ProductBrandGraphQLType::class,
                \App\GraphQL\Ecommerce\Admin\Types\ProductVariationGraphQLType::class,
                \App\GraphQL\Ecommerce\Admin\Types\OrderGraphQLType::class,
                \App\GraphQL\Ecommerce\Admin\Types\OrderItemGraphQLType::class,
                \App\GraphQL\Ecommerce\Admin\Types\OrderInputGraphQLType::class,
                \App\GraphQL\Ecommerce\Admin\Types\ProductJoinOptionGraphQLType::class,
                \App\GraphQL\Ecommerce\Admin\Types\ProductOptionValueGraphQLType::class,
                \App\GraphQL\Ecommerce\Admin\Types\ProductOptionGraphQLType::class,
                \App\GraphQL\Ecommerce\Client\Types\UserCartInputGraphQLType::class,
                \App\GraphQL\Customer\Types\CustomerInputGraphQLType::class,
                \App\GraphQL\Ecommerce\Client\Types\UserGraphQLType::class,
                \App\GraphQL\Ecommerce\Client\Types\UserCartGraphQLType::class,
                \App\GraphQL\Common\Types\DistrictGraphQLType::class,
                \App\GraphQL\Common\Types\MahallaGraphQLType::class,
                \App\GraphQL\Common\Types\RegionGraphQLType::class,
                \App\GraphQL\Customer\Types\AddressGraphQLType::class,
                \App\GraphQL\Ecommerce\Admin\Types\OrderStatusGraphQLType::class,
                \App\GraphQL\Ecommerce\Client\Types\OrderStatusHistoryGraphQLType::class,
                \App\GraphQL\Ecommerce\Client\Types\CustomerGraphQLType::class,
            ],
            'middleware' => ['auth:ecommerce-client', 'is_soft_delete_client_token', 'ecommerce'],
        ];
    }
}
