<?php

namespace App\GraphQL\TransactionType\Types;

use App\Models\Company\TransactionType;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class TransactionTypeGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'TransactionTypeGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (TransactionType $root) => $root->id,
            ],
            'name' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (TransactionType $root) => $root->name,
            ],
            'name_uz' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (TransactionType $root) => $root->name_uz,
            ],
            'name_ru' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (TransactionType $root) => $root->name_ru,
            ],
            'name_en' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (TransactionType $root) => $root->name_en,
            ],
            'is_system' => [
                'type' => Type::nonNull(Type::boolean()),
                'resolve' => fn (TransactionType $root) => $root->is_system,
            ],
            'created_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (TransactionType $root) => $root->created_at->timestamp,
            ],
            'updated_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (TransactionType $root) => $root->updated_at->timestamp,
            ],
            'deleted_at' => [
                'type' => Type::int(),
                'resolve' => fn (TransactionType $root) => $root->deleted_at?->timestamp,
            ],
        ];
    }
}
