<?php

namespace App\GraphQL\Scoring\Types\Katm\StartInfo;

use App\DTOs\Scoring\Katm\StartInfo\Report\KatmStartInfoReportSysInfoDTO;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class KatmStartInfoReportSysInfoGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'KatmStartInfoReportSysInfoGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'declaration' => [
                'type' => Type::string(),
                'resolve' => fn (KatmStartInfoReportSysInfoDTO $root) => $root->declaration,
            ],
            'bank' => [
                'type' => Type::string(),
                'resolve' => fn (KatmStartInfoReportSysInfoDTO $root) => $root->bank,
            ],
            'branch' => [
                'type' => Type::string(),
                'resolve' => fn (KatmStartInfoReportSysInfoDTO $root) => $root->branch,
            ],
            'demand_id' => [
                'type' => Type::string(),
                'resolve' => fn (KatmStartInfoReportSysInfoDTO $root) => $root->demand_id,
            ],
            'date' => [
                'type' => Type::string(),
                'resolve' => fn (KatmStartInfoReportSysInfoDTO $root) => $root->date,
            ],
            'claim_id' => [
                'type' => Type::string(),
                'resolve' => fn (KatmStartInfoReportSysInfoDTO $root) => $root->claim_id,
            ],
            'claim_date' => [
                'type' => Type::string(),
                'resolve' => fn (KatmStartInfoReportSysInfoDTO $root) => $root->claim_date,
            ],
            'report_type' => [
                'type' => Type::string(),
                'resolve' => fn (KatmStartInfoReportSysInfoDTO $root) => $root->report_type,
            ],
        ];
    }
}
