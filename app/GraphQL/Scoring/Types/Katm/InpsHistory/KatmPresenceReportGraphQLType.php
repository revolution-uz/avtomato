<?php

namespace App\GraphQL\Scoring\Types\Katm\InpsHistory;

use App\DTOs\Scoring\Katm\InpsHistory\KatmPresenceReportDTO;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class KatmPresenceReportGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'KatmPresenceReportGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
        'description' => 'Katm INPS Presence Report GraphQL Type',
    ];

    public function fields(): array
    {
        return [
            'num' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Num',
                'resolve' => function (KatmPresenceReportDTO $root) {
                    return $root->num;
                },
            ],
            'report_name' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Report name',
                'resolve' => function (KatmPresenceReportDTO $root) {
                    return $root->report_name;
                },
            ],
            'presence' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Presence',
                'resolve' => function (KatmPresenceReportDTO $root) {
                    return $root->presence;
                },
            ],
        ];
    }
}
