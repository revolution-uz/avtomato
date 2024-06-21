<?php

namespace App\GraphQL\Common\Queries;

use App\GraphQL\Common\Types\MahallaGraphQLType;
use App\Models\Company\Mahalla;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class MahallasGraphQLQuery extends Query
{
    protected $attributes = [
        'name' => 'mahallas',
    ];

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(MahallaGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function args(): array
    {
        return [];
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return Mahalla::query()->get();
    }
}
