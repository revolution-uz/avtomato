<?php

namespace App\GraphQL\Scoring\Types\RoyxatCreditHistory;

use App\DTOs\Scoring\RoyxatCreditHistory\RoyxatCreditHistoryResponseDTO;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class RoyxatCreditHistoryResponseGraphQLType extends GraphQLType
{
    public const string GRAPHQL_TYPE_NAME = 'RoyxatCreditHistoryResponseGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
        'model' => RoyxatCreditHistoryResponseDTO::class,
    ];

    public function fields(): array
    {
        return [
            'ok' => [
                'type' => Type::nonNull(Type::boolean()),
                'resolve' => fn (RoyxatCreditHistoryResponseDTO $root) => $root->ok,
            ],
            'result' => [
                'type' => GraphQL::type(RoyxatCreditHistoryResultGraphQLType::GRAPHQL_TYPE_NAME),
                'resolve' => fn (RoyxatCreditHistoryResponseDTO $root) => $root->result,
            ],
            'error_code' => [
                'type' => Type::int(),
                'resolve' => fn (RoyxatCreditHistoryResponseDTO $root) => $root->error_code,
            ],
            'description' => [
                'type' => Type::string(),
                'resolve' => fn (RoyxatCreditHistoryResponseDTO $root) => $root->description,
            ],
        ];
    }
}
