<?php

namespace App\GraphQL\Ecommerce\Common\Queries;

use App\GraphQL\Common\Types\DistrictGraphQLType;
use App\Models\Company\District;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class DistrictsGraphQLQuery extends Query
{
    protected $attributes = [
        'name' => 'districts',
    ];

    public function type(): Type
    {
        return Type::nonNull(Type::listOf(Type::nonNull(GraphQL::type(DistrictGraphQLType::GRAPHQL_TYPE_NAME))));
    }

    public function args(): array
    {
        return [
            'region_id' => [
                'name' => 'region_id',
                'type' => Type::int(),
                'rules' => ['nullable', 'exists:regions,id'],
            ],
        ];
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        if (isset($args['region_id'])) {
            return District::query()->where('region_id', $args['region_id'])->get();
        }

        return District::query()->get();
    }
}
