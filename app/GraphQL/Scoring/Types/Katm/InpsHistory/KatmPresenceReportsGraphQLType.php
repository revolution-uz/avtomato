<?php

namespace App\GraphQL\Scoring\Types\Katm\InpsHistory;

use App\DTOs\Scoring\Katm\InpsHistory\KatmPresenceReportsDTO;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class KatmPresenceReportsGraphQLType extends GraphQLType
{
    public const string GRAPHQL_TYPE_NAME = 'KatmPresenceReportsGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
        'description' => 'Katm INPS Presence Reports GraphQL Type',
    ];

    public function fields(): array
    {
        return [
            'presence_report' => [
                'type' => Type::listOf(GraphQL::type(KatmPresenceReportGraphQLType::GRAPHQL_TYPE_NAME)),
                'description' => 'Presence report',
                'resolve' => function (KatmPresenceReportsDTO $root) {
                    return $root->presence_report;
                },
            ],
        ];
    }
}
