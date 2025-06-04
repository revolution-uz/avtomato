<?php

namespace App\GraphQL\Scoring\Types\Katm\StartInfo;

use App\DTOs\Scoring\Katm\StartInfo\Report23\KatmStartInfoReport23ClaimQuantityDTO;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class KatmStartInfoReport23ClaimQuantityGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'KatmStartInfoReport23ClaimQuantityGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'claims_qty' => [
                'type' => Type::int(),
                'resolve' => fn (KatmStartInfoReport23ClaimQuantityDTO $root) => $root->claims_qty,
            ],
            'rejected_claims_qty' => [
                'type' => Type::int(),
                'resolve' => fn (KatmStartInfoReport23ClaimQuantityDTO $root) => $root->rejected_claims_qty,
            ],
            'contracts_qty' => [
                'type' => Type::int(),
                'resolve' => fn (KatmStartInfoReport23ClaimQuantityDTO $root) => $root->contracts_qty,
            ],
            'unresolved_claims_qty' => [
                'type' => Type::int(),
                'resolve' => fn (KatmStartInfoReport23ClaimQuantityDTO $root) => $root->unresolved_claims_qty,
            ],
        ];
    }
}
