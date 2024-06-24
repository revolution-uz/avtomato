<?php

namespace App\GraphQL\Scoring\Types\PensionHistory;

use App\DTOs\Scoring\Katm\PensionInfo\KatmPensionInfoResponseDTO;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class KatmPensionInfoResponseGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'KatmPensionInfoResponseGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
        'description' => 'Katm Pension Info Response GraphQL Type',
    ];

    public function fields(): array
    {
        return [
            'result' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Result',
                'resolve' => function (KatmPensionInfoResponseDTO $root) {
                    return $root->result;
                },
            ],
            'sizePension' => [
                'type' => GraphQL::type(KatmPensionSizePensionGraphQLType::GRAPHQL_TYPE_NAME),
                'description' => 'Size Pension',
                'resolve' => function (KatmPensionInfoResponseDTO $root) {
                    return $root->sizePension;
                },
            ],
            'comments' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Comments',
                'resolve' => function (KatmPensionInfoResponseDTO $root) {
                    return $root->comments;
                },
            ],
        ];
    }
}
