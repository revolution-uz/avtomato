<?php

namespace App\GraphQL\Market;

use App\GraphQL\Market\Mutations\CreateProductCategoryGraphQLMutation;
use App\GraphQL\Market\Queries\ProductCategoriesGraphQLQuery;
use App\GraphQL\Market\Queries\ProductsGraphQLQuery;
use App\GraphQL\Market\Types\BannerGraphQLType;
use App\GraphQL\Market\Types\ProductCategoryGraphQLType;
use App\GraphQL\Market\Types\ProductCategoryInputGraphQLType;
use App\GraphQL\Market\Types\ProductGraphQLType;
use App\GraphQL\Market\Types\ProductInputGraphQLType;
use Rebing\GraphQL\Support\Contracts\ConfigConvertible;

class MarketGraphQLSchema implements ConfigConvertible
{
    public function toConfig(): array
    {
        return [
            'mutation' => [
                CreateProductCategoryGraphQLMutation::class,
            ],
            'query' => [
                ProductCategoriesGraphQLQuery::class,
                ProductsGraphQLQuery::class,
            ],
            'types' => [
                ProductCategoryGraphQLType::class,
                ProductCategoryInputGraphQLType::class,
                ProductGraphQLType::class,
                BannerGraphQLType::class,
                ProductInputGraphQLType::class,
            ],
        ];
    }
}
