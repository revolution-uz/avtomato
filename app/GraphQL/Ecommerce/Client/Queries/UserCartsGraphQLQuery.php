<?php

namespace App\GraphQL\Ecommerce\Client\Queries;

use App\GraphQL\Ecommerce\Client\Types\UserCartGraphQLType;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class UserCartsGraphQLQuery extends Query
{
    protected $attributes = [
        'name' => 'userCarts',
    ];

    public function type(): Type
    {
        return Type::listOf(GraphQL::type(UserCartGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $authUser = auth('ecommerce-client')->user();

        return $authUser->carts()->orderBy('id', 'desc')->get();
    }
}
