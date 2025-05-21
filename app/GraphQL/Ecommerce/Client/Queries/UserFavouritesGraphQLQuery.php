<?php

namespace App\GraphQL\Ecommerce\Client\Queries;

use App\GraphQL\Ecommerce\Admin\Types\ProductGraphQLType;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class UserFavouritesGraphQLQuery extends Query
{
    protected $attributes = [
        'name' => 'userFavourites',
    ];

    public function type(): Type
    {
        return Type::listOf(GraphQL::type(ProductGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $authUser = auth('ecommerce-client')->user();

        return $authUser->favourites()->orderByPivot('id', 'desc')->get();
    }
}
