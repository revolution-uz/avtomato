<?php

namespace App\GraphQL\EcommerceClient\Common;

use App\GraphQL\Ecommerce\Types\BannerGraphQLType;
use App\GraphQL\Ecommerce\Types\EcommerceImageGraphQLType;
use App\GraphQL\Ecommerce\Types\ProductBrandGraphQLType;
use App\GraphQL\Ecommerce\Types\ProductCategoryGraphQLType;
use App\GraphQL\Ecommerce\Types\ProductGraphQLType;
use App\GraphQL\Ecommerce\Types\ProductJoinOptionGraphQLType;
use App\GraphQL\Ecommerce\Types\ProductOptionGraphQLType;
use App\GraphQL\Ecommerce\Types\ProductOptionValueGraphQLType;
use App\GraphQL\EcommerceClient\Common\Mutation\LoginGraphQLMutation;
use App\GraphQL\EcommerceClient\Common\Mutation\RegisterGraphQLMutation;
use App\GraphQL\EcommerceClient\Common\Queries\BannersGraphQLQuery;
use App\GraphQL\EcommerceClient\Common\Queries\ProductBrandsGraphQLQuery;
use App\GraphQL\EcommerceClient\Common\Queries\ProductCategoriesGraphQLQuery;
use App\GraphQL\EcommerceClient\Common\Queries\ProductsGraphQLQuery;
use App\GraphQL\EcommerceClient\Common\Queries\SingleBannerGraphQLQuery;
use App\GraphQL\EcommerceClient\Common\Queries\SingleProductBrandGraphQLQuery;
use App\GraphQL\EcommerceClient\Common\Queries\SingleProductCategoryGraphQLQuery;
use App\GraphQL\EcommerceClient\Common\Queries\SingleProductGraphQLQuery;
use Rebing\GraphQL\Support\Contracts\ConfigConvertible;

class EcommerceClientCommonGraphQLSchema implements ConfigConvertible
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
            ],
            'mutation' => [
                RegisterGraphQLMutation::class,
                LoginGraphQLMutation::class,
            ],
            'types' => [
                EcommerceImageGraphQLType::class,
                ProductCategoryGraphQLType::class,
                ProductGraphQLType::class,
                BannerGraphQLType::class,
                ProductBrandGraphQLType::class,
                ProductJoinOptionGraphQLType::class,
                ProductOptionValueGraphQLType::class,
                ProductOptionGraphQLType::class,
            ],
            'middleware' => ['optional_auth:sanctum'],
        ];
    }
}
