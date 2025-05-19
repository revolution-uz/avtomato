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
        return Type::nonNull(Type::listOf(Type::nonNull(GraphQL::type(MahallaGraphQLType::GRAPHQL_TYPE_NAME))));
    }

    public function args(): array
    {
        return [
            'district_id' => [
                'name' => 'district_id',
                'type' => Type::int(),
                'rules' => ['nullable', 'exists:districts,id'],
            ],
        ];
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        if (isset($args['district_id'])) {
            return Mahalla::query()->where('district_id', $args['district_id'])->get();
        }

        return Mahalla::query()->get();
    }
}
