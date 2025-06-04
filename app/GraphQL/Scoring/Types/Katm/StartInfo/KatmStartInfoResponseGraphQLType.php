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
    ];

    public function fields(): array
    {
        return [
            'short' => [
                'type' => GraphQL::type(KatmStartInfoReportGraphQLType::GRAPHQL_TYPE_NAME),
                'resolve' => fn (KatmStartInfoResponseDTO $root) => $root->report,
            ],
            'full' => [
                'type' => GraphQL::type(KatmStartInfoReport23GraphQLType::GRAPHQL_TYPE_NAME),
                'resolve' => fn (KatmStartInfoResponseDTO $root) => $root->report23,
            ],
        ];
    }
}
