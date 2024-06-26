<?php

namespace App\GraphQL\Scoring\Types\Debt;

use App\DTOs\Scoring\Plum\Mib\MibInfoResponseDTO;
use App\DTOs\Scoring\Plum\Tax\TaxInfoResponseDTO;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class DebtScoringResponseGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'DebtScoringResponseGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'debtBal' => [
                'name' => 'debtBal',
                'type' => Type::nonNull(Type::float()),
                'resolve' => fn (TaxInfoResponseDTO|MibInfoResponseDTO $root) => $root->debtBal,
            ],
        ];
    }
}
