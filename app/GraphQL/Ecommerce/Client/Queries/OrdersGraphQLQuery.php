<?php

namespace App\GraphQL\Ecommerce\Client\Queries;

use App\GraphQL\Ecommerce\Admin\Types\OrderGraphQLType;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class OrdersGraphQLQuery extends Query
{
    protected $attributes = [
        'name' => 'orders',
    ];

    public function type(): Type
    {
        return Type::listOf(GraphQL::type(OrderGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $authUser = auth('ecommerce-client')->user();

        return $authUser->orders()->orderBy('created_at', 'desc')->get();
    }
}
