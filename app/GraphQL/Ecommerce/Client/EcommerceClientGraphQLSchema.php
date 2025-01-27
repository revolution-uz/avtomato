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
use App\GraphQL\Ecommerce\Admin\Types\ProductBrandGraphQLType;
use App\GraphQL\Ecommerce\Admin\Types\ProductCategoryGraphQLType;
use App\GraphQL\Ecommerce\Admin\Types\ProductGraphQLType;
use App\GraphQL\Ecommerce\Admin\Types\ProductJoinOptionGraphQLType;
use App\GraphQL\Ecommerce\Admin\Types\ProductOptionGraphQLType;
use App\GraphQL\Ecommerce\Admin\Types\ProductOptionValueGraphQLType;
use App\GraphQL\Ecommerce\Client\Mutations\AddCartGraphQLMutation;
use App\GraphQL\Ecommerce\Client\Mutations\AddFavouriteGraphQLMutation;
use App\GraphQL\Ecommerce\Client\Mutations\CreateCustomerGraphQLMutation;
use App\GraphQL\Ecommerce\Client\Mutations\CreateOrderGraphQLMutation;
use App\GraphQL\Ecommerce\Client\Mutations\RemoveCartGraphQLMutation;
use App\GraphQL\Ecommerce\Client\Mutations\RemoveFavouriteGraphQLMutation;
use App\GraphQL\Ecommerce\Client\Queries\OrdersGraphQLQuery;
use App\GraphQL\Ecommerce\Client\Queries\UserCartGraphQLQuery;
use App\GraphQL\Ecommerce\Client\Queries\UserFavouritesGraphQLQuery;
use App\GraphQL\Ecommerce\Client\Types\UserCartGraphQLType;
use App\GraphQL\Ecommerce\Client\Types\UserCartInputGraphQLType;
use App\GraphQL\Ecommerce\Client\Types\UserFavouriteGraphQLType;
use App\GraphQL\Ecommerce\Client\Types\UserGraphQLType;
use App\GraphQL\Ecommerce\Common\Queries\BannersGraphQLQuery;
use App\GraphQL\Ecommerce\Common\Queries\ProductBrandsGraphQLQuery;
use App\GraphQL\Ecommerce\Common\Queries\ProductCategoriesGraphQLQuery;
use App\GraphQL\Ecommerce\Common\Queries\ProductsGraphQLQuery;
use App\GraphQL\Ecommerce\Common\Queries\SingleBannerGraphQLQuery;
use App\GraphQL\Ecommerce\Common\Queries\SingleProductBrandGraphQLQuery;
use App\GraphQL\Ecommerce\Common\Queries\SingleProductCategoryGraphQLQuery;
use App\GraphQL\Ecommerce\Common\Queries\SingleProductGraphQLQuery;
use App\GraphQL\Management\Types\AdminInputGraphQLType;
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
                OrderGraphQLType::class,
                OrderItemGraphQLType::class,
                OrderInputGraphQLType::class,
                ProductJoinOptionGraphQLType::class,
                ProductOptionValueGraphQLType::class,
                ProductOptionGraphQLType::class,
                UserCartInputGraphQLType::class,
                CustomerInputGraphQLType::class,
                UserGraphQLType::class,
                UserFavouriteGraphQLType::class,
                UserCartGraphQLType::class,
                DistrictGraphQLType::class,
                MahallaGraphQLType::class,
                RegionGraphQLType::class,
                AddressGraphQLType::class,
                AdminInputGraphQLType::class,
            ],
            'middleware' => ['auth:sanctum', 'is_soft_delete_client_token'],
        ];
    }
}
