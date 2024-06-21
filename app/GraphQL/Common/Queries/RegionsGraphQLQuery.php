<?php

namespace App\GraphQL\Common\Queries;

use App\GraphQL\Common\Types\RegionGraphQLType;
use App\Models\Company\Region;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class RegionsGraphQLQuery extends Query
{
    protected $attributes = [
        'name' => 'regions',
    ];

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(RegionGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function args(): array
    {
        return [];
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return Region::query()->get();
    }
}
