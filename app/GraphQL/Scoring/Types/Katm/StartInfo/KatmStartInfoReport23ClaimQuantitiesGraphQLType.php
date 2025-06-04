<?php

namespace App\GraphQL\Scoring\Types\Katm\StartInfo;

use App\DTOs\Scoring\Katm\StartInfo\Report23\KatmStartInfoReport23ClaimQuantitiesDTO;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class KatmStartInfoReport23ClaimQuantitiesGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'KatmStartInfoReport23ClaimQuantitiesGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'bank_claims_qty' => [
                'type' => GraphQL::type(KatmStartInfoReport23ClaimQuantityGraphQLType::GRAPHQL_TYPE_NAME),
                'resolve' => fn (KatmStartInfoReport23ClaimQuantitiesDTO $root) => $root->bank_claims_qty,
            ],
            'leasing_claims_qty' => [
                'type' => GraphQL::type(KatmStartInfoReport23ClaimQuantityGraphQLType::GRAPHQL_TYPE_NAME),
                'resolve' => fn (KatmStartInfoReport23ClaimQuantitiesDTO $root) => $root->leasing_claims_qty,
            ],
            'retail_claims_qty' => [
                'type' => GraphQL::type(KatmStartInfoReport23ClaimQuantityGraphQLType::GRAPHQL_TYPE_NAME),
                'resolve' => fn (KatmStartInfoReport23ClaimQuantitiesDTO $root) => $root->retail_claims_qty,
            ],
            'lombard_claims_qty' => [
                'type' => GraphQL::type(KatmStartInfoReport23ClaimQuantityGraphQLType::GRAPHQL_TYPE_NAME),
                'resolve' => fn (KatmStartInfoReport23ClaimQuantitiesDTO $root) => $root->lombard_claims_qty,
            ],
            'mko_claims_qty' => [
                'type' => GraphQL::type(KatmStartInfoReport23ClaimQuantityGraphQLType::GRAPHQL_TYPE_NAME),
                'resolve' => fn (KatmStartInfoReport23ClaimQuantitiesDTO $root) => $root->mko_claims_qty,
            ],
            'insurance_claims_qty' => [
                'type' => GraphQL::type(KatmStartInfoReport23ClaimQuantityGraphQLType::GRAPHQL_TYPE_NAME),
                'resolve' => fn (KatmStartInfoReport23ClaimQuantitiesDTO $root) => $root->insurance_claims_qty,
            ],
            'total_claims_qty' => [
                'type' => GraphQL::type(KatmStartInfoReport23TotalClaimQuantityGraphQLType::GRAPHQL_TYPE_NAME),
                'resolve' => fn (KatmStartInfoReport23ClaimQuantitiesDTO $root) => $root->total_claims_qty,
            ],
        ];
    }
}
