<?php

namespace App\GraphQL\Scoring\Types\UzumNasiya;

use App\DTOs\Scoring\UzumNasiya\UzumNasiyaAvailablePeriodResponseDTO;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class UzumNasiyaAvailablePeriodResponseGraphQLType extends GraphQLType
{
    public const string GRAPHQL_TYPE_NAME = 'UzumNasiyaAvailablePeriodResponseGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'period' => [
                'name' => 'period',
                'type' => Type::string(),
                'resolve' => fn (UzumNasiyaAvailablePeriodResponseDTO $root) => $root->period,
            ],
            'title_uz' => [
                'name' => 'title_uz',
                'type' => Type::string(),
                'resolve' => fn (UzumNasiyaAvailablePeriodResponseDTO $root) => $root->title_uz,
            ],
            'title_ru' => [
                'name' => 'title_ru',
                'type' => Type::string(),
                'resolve' => fn (UzumNasiyaAvailablePeriodResponseDTO $root) => $root->title_ru,
            ],
            'available_balance' => [
                'name' => 'available_balance',
                'type' => Type::string(),
                'resolve' => fn (UzumNasiyaAvailablePeriodResponseDTO $root) => $root->available_balance,
            ],
        ];
    }
}
