<?php

namespace App\GraphQL\Ecommerce\Common\Queries;

use App\GraphQL\Common\Types\MahallaGraphQLType;
use App\Models\Company\Mahalla;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Database\Eloquent\Builder;
use Rebing\GraphQL\Support\Query;

class MahallasGraphQLQuery extends Query
{
    protected $attributes = [
        'name' => 'mahallas',
    ];

    public function type(): Type
    {
        return Type::nonNull(Type::listOf(Type::nonNull(MahallaGraphQLType::type())));
    }

    public function args(): array
    {
        return [
            'district_id' => [
                'name' => 'district_id',
                'type' => Type::int(),
                'rules' => 'nullable|exists:districts,id',
            ],
        ];
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return Mahalla::query()
            ->when(isset($args['district_id']), fn (Builder|Mahalla $q) => $q
                ->where('district_id', $args['district_id']))
            ->get();
    }
}
