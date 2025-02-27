<?php

namespace App\GraphQL\LoyaltyProgram\Common\Queries;

use App\GraphQL\LoyaltyProgram\Common\Types\TariffGraphQLType;
use App\Models\Company\LoyaltyProgram\Tariff;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class SingleTariffGraphQLQuery extends Query
{
    protected $attributes = [
        'name' => 'singleTariff',
    ];

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(TariffGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function args(): array
    {
        return [
            'id' => [
                'name' => 'id',
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    Rule::exists(Tariff::class, 'id')->whereNull('deleted_at'),
                ],
            ],
        ];
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return Tariff::query()->findOrFail($args['id']);
    }
}
