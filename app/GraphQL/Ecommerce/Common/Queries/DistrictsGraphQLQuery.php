<?php

namespace App\GraphQL\Ecommerce\Common\Queries;

use App\GraphQL\Common\Types\DistrictGraphQLType;
use App\Models\Company\District;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Database\Eloquent\Builder;
use Rebing\GraphQL\Support\Query;

class DistrictsGraphQLQuery extends Query
{
    protected $attributes = [
        'name' => 'districts',
    ];

    public function type(): Type
    {
        return Type::nonNull(Type::listOf(Type::nonNull(DistrictGraphQLType::type())));
    }

    public function args(): array
    {
        return [
            'region_id' => [
                'name' => 'region_id',
                'type' => Type::int(),
                'rules' => 'nullable|exists:regions,id',
            ],
        ];
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return District::query()
            ->when(isset($args['region_id']), fn (Builder|District $q) => $q
                ->where('region_id', $args['region_id']))
            ->get();
    }
}
