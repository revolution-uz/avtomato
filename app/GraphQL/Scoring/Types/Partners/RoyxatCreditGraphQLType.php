<?php

namespace App\GraphQL\Scoring\Types\Partners;

use App\DTOs\Royxat\RoyxatCreditResponseDTO;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class RoyxatCreditGraphQLType extends GraphQLType
{
    public const string GRAPHQL_TYPE_NAME = 'RoyxatCreditGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
        'model' => RoyxatCreditResponseDTO::class,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (RoyxatCreditResponseDTO $root) => $root->id,
            ],
            'credit_id' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (RoyxatCreditResponseDTO $root) => $root->credit_id,
            ],
            'amount' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (RoyxatCreditResponseDTO $root) => $root->amount,
            ],
            'duration' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (RoyxatCreditResponseDTO $root) => $root->duration,
            ],
            'name' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (RoyxatCreditResponseDTO $root) => $root->name,
            ],
            'passport' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (RoyxatCreditResponseDTO $root) => $root->passport,
            ],
            'tin' => [
                'type' => Type::int(),
                'resolve' => fn (RoyxatCreditResponseDTO $root) => $root->tin,
            ],
            'pinfl' => [
                'type' => Type::string(),
                'resolve' => fn (RoyxatCreditResponseDTO $root) => $root->pinfl,
            ],
            'credit_status' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (RoyxatCreditResponseDTO $root) => $root->credit_status,
            ],
            'contracted_date' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (RoyxatCreditResponseDTO $root) => $root->contract_date->timestamp,
            ],
            'overdue_days' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (RoyxatCreditResponseDTO $root) => $root->overdue_days,
            ],
            'merchant_id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (RoyxatCreditResponseDTO $root) => $root->merchant_id,
            ],
            'merchant_name' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (RoyxatCreditResponseDTO $root) => $root->merchant_name,
            ],
            'created_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (RoyxatCreditResponseDTO $root) => $root->created_at->timestamp,
            ],
            'updated_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (RoyxatCreditResponseDTO $root) => $root->updated_at->timestamp,
            ],
        ];
    }
}
