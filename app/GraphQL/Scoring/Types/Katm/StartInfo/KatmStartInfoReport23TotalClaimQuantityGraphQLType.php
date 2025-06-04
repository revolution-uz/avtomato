<?php

namespace App\GraphQL\Scoring\Types\Katm\StartInfo;

use App\DTOs\Scoring\Katm\StartInfo\Report23\KatmStartInfoReport23TotalClaimQuantityDTO;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class KatmStartInfoReport23TotalClaimQuantityGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'KatmStartInfoReport23TotalClaimQuantityGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'all_claims_qty' => [
                'type' => Type::int(),
                'resolve' => fn (KatmStartInfoReport23TotalClaimQuantityDTO $root) => $root->all_claims_qty,
            ],
            'all_rejected_claims_qty' => [
                'type' => Type::int(),
                'resolve' => fn (KatmStartInfoReport23TotalClaimQuantityDTO $root) => $root->all_rejected_claims_qty,
            ],
            'all_contracts_qty' => [
                'type' => Type::int(),
                'resolve' => fn (KatmStartInfoReport23TotalClaimQuantityDTO $root) => $root->all_contracts_qty,
            ],
            'all_unresolved_claims_qty' => [
                'type' => Type::int(),
                'resolve' => fn (KatmStartInfoReport23TotalClaimQuantityDTO $root) => $root->all_unresolved_claims_qty,
            ],
        ];
    }
}
