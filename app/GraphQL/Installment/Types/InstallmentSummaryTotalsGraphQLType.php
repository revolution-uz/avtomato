<?php

namespace App\GraphQL\Installment\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class InstallmentSummaryTotalsGraphQLType extends GraphQLType
{
    public const string GRAPHQL_TYPE_NAME = 'InstallmentSummaryTotalsGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'total_amount' => [
                'type' => Type::float(),
                'resolve' => fn ($root) => $root->total_amount,
            ],
            'total_installment_amount' => [
                'type' => Type::float(),
                'resolve' => fn ($root) => $root->total_installment_amount,
            ],
            'total_initial_fee' => [
                'type' => Type::float(),
                'resolve' => fn ($root) => $root->total_initial_fee,
            ],
            'total_debt' => [
                'type' => Type::float(),
                'resolve' => fn ($root) => $root->total_debt_amount,
            ],
        ];
    }
}
