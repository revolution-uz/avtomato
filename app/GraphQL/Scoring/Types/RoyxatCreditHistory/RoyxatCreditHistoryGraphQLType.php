<?php

namespace App\GraphQL\Scoring\Types\RoyxatCreditHistory;

use App\DTOs\Scoring\RoyxatCreditHistory\RoyxatCreditHistoryResponseDTO;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class RoyxatCreditHistoryGraphQLType extends GraphQLType
{
    public const string GRAPHQL_TYPE_NAME = 'RoyxatCreditHistoryGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
        'model' => RoyxatCreditHistoryResponseDTO::class,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (RoyxatCreditHistoryResponseDTO $root) => $root->id,
            ],
            'credit_id' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (RoyxatCreditHistoryResponseDTO $root) => $root->credit_id,
            ],
            'amount' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (RoyxatCreditHistoryResponseDTO $root) => $root->amount,
            ],
            'duration' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (RoyxatCreditHistoryResponseDTO $root) => $root->duration,
            ],
            'name' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (RoyxatCreditHistoryResponseDTO $root) => $root->name,
            ],
            'passport' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (RoyxatCreditHistoryResponseDTO $root) => $root->passport,
            ],
            'tin' => [
                'type' => Type::int(),
                'resolve' => fn (RoyxatCreditHistoryResponseDTO $root) => $root->tin,
            ],
            'pinfl' => [
                'type' => Type::string(),
                'resolve' => fn (RoyxatCreditHistoryResponseDTO $root) => $root->pinfl,
            ],
            'credit_status' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (RoyxatCreditHistoryResponseDTO $root) => $root->credit_status,
            ],
            'contracted_date' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (RoyxatCreditHistoryResponseDTO $root) => $root->contract_date->timestamp,
            ],
            'overdue_days' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (RoyxatCreditHistoryResponseDTO $root) => $root->overdue_days,
            ],
            'merchant_id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (RoyxatCreditHistoryResponseDTO $root) => $root->merchant_id,
            ],
            'merchant_name' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (RoyxatCreditHistoryResponseDTO $root) => $root->merchant_name,
            ],
            'created_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (RoyxatCreditHistoryResponseDTO $root) => $root->created_at->timestamp,
            ],
            'updated_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (RoyxatCreditHistoryResponseDTO $root) => $root->updated_at->timestamp,
            ],
        ];
    }
}
