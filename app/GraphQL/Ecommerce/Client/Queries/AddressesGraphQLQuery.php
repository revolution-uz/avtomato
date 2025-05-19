<?php

namespace App\GraphQL\Ecommerce\Client\Queries;

use App\GraphQL\Customer\Types\AddressGraphQLType;
use App\Models\Company\Ecommerce\User;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class AddressesGraphQLQuery extends Query
{
    protected $attributes = [
        'name' => 'addresses',
    ];

    public function type(): Type
    {
        return Type::listOf(GraphQL::type(AddressGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        /** @var User $user */
        $user = auth('ecommerce-client')->user();

        return $user->addresses()->orderBy('created_at', 'desc')->get();
    }
}
