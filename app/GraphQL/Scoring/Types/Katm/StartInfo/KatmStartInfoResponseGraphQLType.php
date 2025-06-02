<?php

namespace App\GraphQL\Scoring\Types\Katm\StartInfo;

use App\DTOs\Scoring\Katm\StartInfo\KatmStartInfoResponseDTO;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class KatmStartInfoResponseGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'KatmStartInfoResponseGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
        'description' => 'Katm Pension Info Response GraphQL Type',
    ];

    public function fields(): array
    {
        return [
            'short' => [
                'type' => GraphQL::type(KatmStartInfoReportGraphQLType::GRAPHQL_TYPE_NAME),
                'description' => 'Short report of Start Info',
                'resolve' => function (KatmStartInfoResponseDTO $root) {
                    return $root->report;
                },
            ],
            'full' => [
                'type' => GraphQL::type(KatmStartInfoReport23GraphQLType::GRAPHQL_TYPE_NAME),
                'description' => 'Full report of Start Info',
                'resolve' => function (KatmStartInfoResponseDTO $root) {
                    return $root->report23;
                },
            ],
        ];
    }
}
