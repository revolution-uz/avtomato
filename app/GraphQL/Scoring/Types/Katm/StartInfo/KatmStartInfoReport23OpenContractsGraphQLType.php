<?php

namespace App\GraphQL\Scoring\Types\Katm\StartInfo;

use App\DTOs\Scoring\Katm\StartInfo\Report23\KatmStartInfoReport23OpenContractsDTO;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class KatmStartInfoReport23OpenContractsGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'KatmStartInfoReport23OpenContractsGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'open_contract' => [
                'type' => Type::listOf(GraphQL::type(KatmStartInfoReport23OpenContractGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (KatmStartInfoReport23OpenContractsDTO $root) => $root->open_contract,
            ],
            'all_debt_sum' => [
                'type' => Type::int(),
                'resolve' => fn (KatmStartInfoReport23OpenContractsDTO $root) => $root->all_debt_sum,
            ],
            'all_overdue_debt_sum' => [
                'type' => Type::int(),
                'resolve' => fn (KatmStartInfoReport23OpenContractsDTO $root) => $root->all_overdue_debt_sum,
            ],
            'average_monthly_payment' => [
                'type' => Type::int(),
                'resolve' => fn (KatmStartInfoReport23OpenContractsDTO $root) => $root->average_monthly_payment,
            ],
        ];
    }
}
