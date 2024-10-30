<?php

namespace App\GraphQL\Scoring\Types\UzumNasiya;

use App\DTOs\Scoring\UzumNasiya\UzumNasiyaLimitResponseDTO;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class UzumNasiyaLimitResponseGraphQLType extends GraphQLType
{
    public const string GRAPHQL_TYPE_NAME = 'UzumNasiyaLimitScoringResponseGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'status' => [
                'name' => 'status',
                'type' => Type::int(),
                'resolve' => fn (UzumNasiyaLimitResponseDTO $root) => $root->status,
            ],
            'buyer_id' => [
                'name' => 'buyer_id',
                'type' => Type::int(),
                'resolve' => fn (UzumNasiyaLimitResponseDTO $root) => $root->buyer_id,
            ],
            'available_periods' => [
                'name' => 'available_periods',
                'type' => Type::listOf(UzumNasiyaAvailablePeriodScoringResponseGraphQLType::GRAPHQL_TYPE_NAME),
                'resolve' => fn (UzumNasiyaLimitResponseDTO $root) => $root->available_periods,
            ],
            'balance' => [
                'name' => 'balance',
                'type' => Type::string(),
                'resolve' => fn (UzumNasiyaLimitResponseDTO $root) => $root->balance,
            ],
            'has_overdue_contracts' => [
                'name' => 'has_overdue_contracts',
                'type' => Type::boolean(),
                'resolve' => fn (UzumNasiyaLimitResponseDTO $root) => $root->has_overdue_contracts,
            ],
        ];
    }
}
