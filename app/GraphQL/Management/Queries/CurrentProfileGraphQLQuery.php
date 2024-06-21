<?php

namespace App\GraphQL\Management\Queries;

use App\GraphQL\Management\Types\AdminGraphQLType;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class CurrentProfileGraphQLQuery extends Query
{
    protected $attributes = [
        'name' => 'currentProfile',
    ];

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(AdminGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function args(): array
    {
        return [];
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return true;
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return auth()->user();
    }
}
