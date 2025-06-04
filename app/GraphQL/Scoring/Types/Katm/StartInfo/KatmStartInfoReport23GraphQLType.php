<?php

namespace App\GraphQL\Scoring\Types\Katm\StartInfo;

use App\DTOs\Scoring\Katm\StartInfo\Report23\KatmStartInfoReport23DTO;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class KatmStartInfoReport23GraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'KatmStartInfoReport23GraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'sysinfo' => [
                'type' => GraphQL::type(KatmStartInfoReport23SysInfoGraphQLType::GRAPHQL_TYPE_NAME),
                'resolve' => fn (KatmStartInfoReport23DTO $root) => $root->sysinfo,
            ],
            'client' => [
                'type' => GraphQL::type(KatmStartInfoReport23ClientGraphQLType::GRAPHQL_TYPE_NAME),
                'resolve' => fn (KatmStartInfoReport23DTO $root) => $root->client,
            ],
            'overview' => [
                'type' => Type::string(),
                'resolve' => fn (KatmStartInfoReport23DTO $root) => $root->overview,
            ],
            'scorring' => [
                'type' => Type::string(),
                'resolve' => fn (KatmStartInfoReport23DTO $root) => $root->scorring,
            ],
            'open_contracts' => [
                'type' => GraphQL::type(KatmStartInfoReport23OpenContractsGraphQLType::GRAPHQL_TYPE_NAME),
                'resolve' => fn (KatmStartInfoReport23DTO $root) => $root->open_contracts,
            ],
            'claims_wo_contracts' => [
                'type' => Type::string(),
                'resolve' => fn (KatmStartInfoReport23DTO $root) => $root->claims_wo_contracts,
            ],
            'contracts' => [
                'type' => Type::string(),
                'resolve' => fn (KatmStartInfoReport23DTO $root) => $root->contracts,
            ],
            'contingent_liabilities' => [
                'type' => Type::string(),
                'resolve' => fn (KatmStartInfoReport23DTO $root) => $root->contingent_liabilities,
            ],
            'credit_requests' => [
                'type' => Type::string(),
                'resolve' => fn (KatmStartInfoReport23DTO $root) => $root->credit_requests,
            ],
            'subscriptions' => [
                'type' => Type::string(),
                'resolve' => fn (KatmStartInfoReport23DTO $root) => $root->subscriptions,
            ],
            'blacklist' => [
                'type' => Type::string(),
                'resolve' => fn (KatmStartInfoReport23DTO $root) => $root->blacklist,
            ],
            'claims_qty' => [
                'type' => GraphQL::type(KatmStartInfoReport23ClaimQuantitiesGraphQLType::GRAPHQL_TYPE_NAME),
                'resolve' => fn (KatmStartInfoReport23DTO $root) => $root->claims_qty,
            ],
        ];
    }
}
