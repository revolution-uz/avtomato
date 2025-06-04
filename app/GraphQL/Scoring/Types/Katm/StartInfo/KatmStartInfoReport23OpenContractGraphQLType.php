<?php

namespace App\GraphQL\Scoring\Types\Katm\StartInfo;

use App\DTOs\Scoring\Katm\StartInfo\Report23\KatmStartInfoReport23OpenContractDTO;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class KatmStartInfoReport23OpenContractGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'KatmStartInfoReport23OpenContractGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'org' => [
                'type' => Type::string(),
                'resolve' => fn (KatmStartInfoReport23OpenContractDTO $root) => $root->org,
            ],
            'branch' => [
                'type' => Type::string(),
                'resolve' => fn (KatmStartInfoReport23OpenContractDTO $root) => $root->branch,
            ],
            'org_name' => [
                'type' => Type::string(),
                'resolve' => fn (KatmStartInfoReport23OpenContractDTO $root) => $root->org_name,
            ],
            'contract_id' => [
                'type' => Type::string(),
                'resolve' => fn (KatmStartInfoReport23OpenContractDTO $root) => $root->contract_id,
            ],
            'currency' => [
                'type' => Type::string(),
                'resolve' => fn (KatmStartInfoReport23OpenContractDTO $root) => $root->currency,
            ],
            'total_debt_sum' => [
                'type' => Type::int(),
                'resolve' => fn (KatmStartInfoReport23OpenContractDTO $root) => $root->total_debt_sum,
            ],
            'overdue_debt_sum' => [
                'type' => Type::int(),
                'resolve' => fn (KatmStartInfoReport23OpenContractDTO $root) => $root->overdue_debt_sum,
            ],
            'monthly_average_payment' => [
                'type' => Type::int(),
                'resolve' => fn (KatmStartInfoReport23OpenContractDTO $root) => $root->monthly_average_payment,
            ],
        ];
    }
}
