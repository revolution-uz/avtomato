<?php

namespace App\GraphQL\LoyaltyProgram\Client\Queries;

use App\GraphQL\LoyaltyProgram\Client\Types\ProfileGraphQLType;
use App\Models\Company\LoyaltyProgram\Subscription;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class ProfileMeGraphQLQuery extends Query
{
    protected $attributes = [
        'name' => 'profileMe',
    ];

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(ProfileGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        // Subscription::factory()->create();
        //
        // dd(1);

        return auth('loyalty-program-client')->user();
    }
}
