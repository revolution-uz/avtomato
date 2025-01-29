<?php

namespace App\GraphQL\Ecommerce\Client;

use App\GraphQL\Common\Types\DistrictGraphQLType;
use App\GraphQL\Common\Types\MahallaGraphQLType;
use App\GraphQL\Common\Types\RegionGraphQLType;
use App\GraphQL\Customer\Types\AddressGraphQLType;
use App\GraphQL\Customer\Types\CustomerInputGraphQLType;
use App\GraphQL\Ecommerce\Admin\Types\BannerGraphQLType;
use App\GraphQL\Ecommerce\Admin\Types\EcommerceImageGraphQLType;
use App\GraphQL\Ecommerce\Admin\Types\OrderGraphQLType;
use App\GraphQL\Ecommerce\Admin\Types\OrderInputGraphQLType;
use App\GraphQL\Ecommerce\Admin\Types\OrderItemGraphQLType;
use App\GraphQL\Ecommerce\Admin\Types\OrderStatusGraphQLType;
use App\GraphQL\Ecommerce\Admin\Types\ProductBrandGraphQLType;
use App\GraphQL\Ecommerce\Admin\Types\ProductCategoryGraphQLType;
use App\GraphQL\Ecommerce\Admin\Types\ProductGraphQLType;
use App\GraphQL\Ecommerce\Admin\Types\ProductJoinOptionGraphQLType;
use App\GraphQL\Ecommerce\Admin\Types\ProductOptionGraphQLType;
use App\GraphQL\Ecommerce\Admin\Types\ProductOptionValueGraphQLType;
use App\GraphQL\Ecommerce\Admin\Types\ProductVariationGraphQLType;
use App\GraphQL\Ecommerce\Client\Mutations\AddCartGraphQLMutation;
use App\GraphQL\Ecommerce\Client\Mutations\AddFavouriteGraphQLMutation;
use App\GraphQL\Ecommerce\Client\Mutations\CreateCustomerGraphQLMutation;
use App\GraphQL\Ecommerce\Client\Mutations\CreateOrderGraphQLMutation;
use App\GraphQL\Ecommerce\Client\Mutations\RemoveCartGraphQLMutation;
use App\GraphQL\Ecommerce\Client\Mutations\RemoveFavouriteGraphQLMutation;
use App\GraphQL\Ecommerce\Client\Queries\OrdersGraphQLQuery;
use App\GraphQL\Ecommerce\Client\Queries\UserCartsGraphQLQuery;
use App\GraphQL\Ecommerce\Client\Queries\UserFavouritesGraphQLQuery;
use App\GraphQL\Ecommerce\Client\Types\CustomerGraphQLType;
use App\GraphQL\Ecommerce\Client\Types\OrderStatusHistoryGraphQLType;
use App\GraphQL\Ecommerce\Client\Types\UserCartGraphQLType;
use App\GraphQL\Ecommerce\Client\Types\UserCartInputGraphQLType;
use App\GraphQL\Ecommerce\Client\Types\UserGraphQLType;
use App\GraphQL\Management\Types\AdminInputGraphQLType;
use Rebing\GraphQL\Support\Contracts\ConfigConvertible;

class EcommerceClientGraphQLSchema implements ConfigConvertible
{
    public function toConfig(): array
    {
        return [
            'query' => [
                OrdersGraphQLQuery::class,
                UserCartsGraphQLQuery::class,
                UserFavouritesGraphQLQuery::class,
            ],
            'mutation' => [
                AddCartGraphQLMutation::class,
                AddFavouriteGraphQLMutation::class,
                RemoveCartGraphQLMutation::class,
                RemoveFavouriteGraphQLMutation::class,
                CreateOrderGraphQLMutation::class,
                CreateCustomerGraphQLMutation::class,
            ],
            'types' => [
                EcommerceImageGraphQLType::class,
                ProductCategoryGraphQLType::class,
                ProductGraphQLType::class,
                BannerGraphQLType::class,
                ProductBrandGraphQLType::class,
                ProductVariationGraphQLType::class,
                OrderGraphQLType::class,
                OrderItemGraphQLType::class,
                OrderInputGraphQLType::class,
                ProductJoinOptionGraphQLType::class,
                ProductOptionValueGraphQLType::class,
                ProductOptionGraphQLType::class,
                UserCartInputGraphQLType::class,
                CustomerInputGraphQLType::class,
                UserGraphQLType::class,
                UserCartGraphQLType::class,
                DistrictGraphQLType::class,
                MahallaGraphQLType::class,
                RegionGraphQLType::class,
                AddressGraphQLType::class,
                AdminInputGraphQLType::class,
                OrderStatusGraphQLType::class,
                OrderStatusHistoryGraphQLType::class,
                CustomerGraphQLType::class,
            ],
            'middleware' => ['auth:ecommerce-client', 'is_soft_delete_client_token', 'ecommerce'],
        ];
    }
}
