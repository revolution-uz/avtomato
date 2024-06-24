<?php

namespace App\GraphQL\Scoring\Types\PensionHistory;

use App\DTOs\Scoring\Katm\PensionInfo\KatmPensionSizePensionDTO;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class KatmPensionSizePensionGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'KatmPensionSizePensionGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
        'description' => 'Katm Pension Size Pension GraphQL Type',
    ];

    public function fields(): array
    {
        return [
            'data' => [
                'type' => GraphQL::type(KatmPensionDataGraphQLType::GRAPHQL_TYPE_NAME),
                'description' => 'Data',
                'resolve' => function (KatmPensionSizePensionDTO $root) {
                    return $root->data;
                },
            ],
            'row' => [
                'type' => Type::listOf(GraphQL::type(KatmPensionRowGraphQLType::GRAPHQL_TYPE_NAME)),
                'description' => 'Row',
                'resolve' => function (KatmPensionSizePensionDTO $root) {
                    return $root->row;
                },
            ],
        ];
    }
}
