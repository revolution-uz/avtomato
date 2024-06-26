<?php

namespace App\GraphQL\Scoring\Types\Debt;

use App\DTOs\Scoring\Plum\Mib\MibInfoCreateRequestDTO;
use App\DTOs\Scoring\Plum\Tax\TaxInfoCreateRequestDTO;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class DebtScoringRequestGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'DebtScoringRequestGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'pinfl' => [
                'name' => 'pinfl',
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (TaxInfoCreateRequestDTO|MibInfoCreateRequestDTO $root) => $root->pinfl,
            ],
            'phone' => [
                'name' => 'phone',
                'type' => Type::string(),
                'resolve' => fn (TaxInfoCreateRequestDTO|MibInfoCreateRequestDTO $root) => $root->phone ?? null,
            ],
        ];
    }
}
