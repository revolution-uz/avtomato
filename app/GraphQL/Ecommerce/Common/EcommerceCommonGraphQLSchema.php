<?php

namespace App\GraphQL\Ecommerce\Common;

use App\GraphQL\Common\Queries\DistrictsGraphQLQuery;
use App\GraphQL\Common\Queries\MahallasGraphQLQuery;
use App\GraphQL\Common\Queries\RegionsGraphQLQuery;
use App\GraphQL\Common\Types\DistrictGraphQLType;
use App\GraphQL\Common\Types\MahallaGraphQLType;
use App\GraphQL\Common\Types\RegionGraphQLType;
use App\GraphQL\Ecommerce\Admin\Types\BannerGraphQLType;
use App\GraphQL\Ecommerce\Admin\Types\EcommerceImageGraphQLType;
use App\GraphQL\Ecommerce\Admin\Types\ProductBrandGraphQLType;
use App\GraphQL\Ecommerce\Admin\Types\ProductCategoryGraphQLType;
use App\GraphQL\Ecommerce\Admin\Types\ProductGraphQLType;
use App\GraphQL\Ecommerce\Admin\Types\ProductJoinOptionGraphQLType;
use App\GraphQL\Ecommerce\Admin\Types\ProductOptionGraphQLType;
use App\GraphQL\Ecommerce\Admin\Types\ProductOptionValueGraphQLType;
use App\GraphQL\Ecommerce\Common\Mutations\LoginGraphQLMutation;
use App\GraphQL\Ecommerce\Common\Mutations\RegisterGraphQLMutation;
use App\GraphQL\Ecommerce\Common\Queries\BannersGraphQLQuery;
use App\GraphQL\Ecommerce\Common\Queries\ProductBrandsGraphQLQuery;
use App\GraphQL\Ecommerce\Common\Queries\ProductCategoriesGraphQLQuery;
use App\GraphQL\Ecommerce\Common\Queries\ProductsGraphQLQuery;
use App\GraphQL\Ecommerce\Common\Queries\SingleBannerGraphQLQuery;
use App\GraphQL\Ecommerce\Common\Queries\SingleProductBrandGraphQLQuery;
use App\GraphQL\Ecommerce\Common\Queries\SingleProductCategoryGraphQLQuery;
use App\GraphQL\Ecommerce\Common\Queries\SingleProductGraphQLQuery;
use Rebing\GraphQL\Support\Contracts\ConfigConvertible;

class EcommerceCommonGraphQLSchema implements ConfigConvertible
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
                DistrictsGraphQLQuery::class,
                MahallasGraphQLQuery::class,
                RegionsGraphQLQuery::class,
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
                DistrictGraphQLType::class,
                MahallaGraphQLType::class,
                RegionGraphQLType::class,
            ],
            'middleware' => ['optional_auth:sanctum'],
        ];
    }
}
