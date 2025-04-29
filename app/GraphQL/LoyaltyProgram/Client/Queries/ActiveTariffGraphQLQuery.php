<?php

namespace App\GraphQL\LoyaltyProgram\Client\Queries;

use App\GraphQL\LoyaltyProgram\Client\Types\TariffGraphQLType;
use App\Models\Company\LoyaltyProgram\Tariff;
use Closure;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class ActiveTariffGraphQLQuery extends Query
{
    protected $attributes = [
        'name' => 'activeTariff',
    ];

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(TariffGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function args(): array
    {
        return [];
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $tariff = Tariff::query()->latest()->firstWhere('is_active', true);

        if (! $tariff) {
            throw new Error('There is no active tariff');
        }

        return $tariff;
    }
}
