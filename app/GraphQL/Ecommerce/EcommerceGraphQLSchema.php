<?php

namespace App\GraphQL\Ecommerce;

use App\GraphQL\Ecommerce\Queries\ProductBrandsGraphQLQuery;
use App\GraphQL\Ecommerce\Queries\ProductCategoriesGraphQLQuery;
use App\GraphQL\Ecommerce\Queries\ProductsGraphQLQuery;
use App\GraphQL\Ecommerce\Types\BannerGraphQLType;
use App\GraphQL\Ecommerce\Types\BannerInputGraphQLType;
use App\GraphQL\Ecommerce\Types\Mutations\CreateBannerGraphQLMutation;
use App\GraphQL\Ecommerce\Types\Mutations\CreateProductCategoryGraphQLMutation;
use App\GraphQL\Ecommerce\Types\Mutations\CreateProductGraphQLMutation;
use App\GraphQL\Ecommerce\Types\ProductBrandGraphQLType;
use App\GraphQL\Ecommerce\Types\ProductCategoryGraphQLType;
use App\GraphQL\Ecommerce\Types\ProductCategoryInputGraphQLType;
use App\GraphQL\Ecommerce\Types\ProductGraphQLType;
use App\GraphQL\Ecommerce\Types\ProductInputGraphQLType;
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
                ProductInputGraphQLType::class,
                ProductBrandGraphQLType::class,
                BannerInputGraphQLType::class,
            ],
        ];
    }
}
