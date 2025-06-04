<?php

namespace App\GraphQL\Scoring\Types\Katm\StartInfo;

use App\DTOs\Scoring\Katm\StartInfo\Report23\KatmStartInfoReport23SysInfoDTO;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class KatmStartInfoReport23SysInfoGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'KatmStartInfoReport23SysInfoGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'report_type' => [
                'type' => Type::string(),
                'resolve' => fn (KatmStartInfoReport23SysInfoDTO $root) => $root->report_type,
            ],
            'report_name' => [
                'type' => Type::string(),
                'resolve' => fn (KatmStartInfoReport23SysInfoDTO $root) => $root->report_name,
            ],
            'org' => [
                'type' => Type::string(),
                'resolve' => fn (KatmStartInfoReport23SysInfoDTO $root) => $root->org,
            ],
            'branch' => [
                'type' => Type::string(),
                'resolve' => fn (KatmStartInfoReport23SysInfoDTO $root) => $root->branch,
            ],
            'org_name' => [
                'type' => Type::string(),
                'resolve' => fn (KatmStartInfoReport23SysInfoDTO $root) => $root->org_name,
            ],
            'user_id' => [
                'type' => Type::string(),
                'resolve' => fn (KatmStartInfoReport23SysInfoDTO $root) => $root->user_id,
            ],
            'demand_id' => [
                'type' => Type::string(),
                'resolve' => fn (KatmStartInfoReport23SysInfoDTO $root) => $root->demand_id,
            ],
            'demand_date_time' => [
                'type' => Type::string(),
                'resolve' => fn (KatmStartInfoReport23SysInfoDTO $root) => $root->demand_date_time,
            ],
            'consent_id' => [
                'type' => Type::string(),
                'resolve' => fn (KatmStartInfoReport23SysInfoDTO $root) => $root->consent_id,
            ],
            'consent_date' => [
                'type' => Type::string(),
                'resolve' => fn (KatmStartInfoReport23SysInfoDTO $root) => $root->consent_date,
            ],
            'claim_id' => [
                'type' => Type::string(),
                'resolve' => fn (KatmStartInfoReport23SysInfoDTO $root) => $root->claim_id,
            ],
            'claim_date' => [
                'type' => Type::string(),
                'resolve' => fn (KatmStartInfoReport23SysInfoDTO $root) => $root->claim_date,
            ],
            'subject_type' => [
                'type' => Type::string(),
                'resolve' => fn (KatmStartInfoReport23SysInfoDTO $root) => $root->subject_type,
            ],
            'notes' => [
                'type' => Type::string(),
                'resolve' => fn (KatmStartInfoReport23SysInfoDTO $root) => $root->notes,
            ],
        ];
    }
}
