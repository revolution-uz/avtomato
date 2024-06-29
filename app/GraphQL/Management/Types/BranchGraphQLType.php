<?php

namespace App\GraphQL\Management\Types;

use App\Models\Company\Branch;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class BranchGraphQLType extends GraphQLType
{
    public const string GRAPHQL_TYPE_NAME = 'BranchGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Branch $root) => $root->id,
            ],
            'name' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (Branch $root) => $root->name,
            ],
            'address' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (Branch $root) => $root->address,
            ],
            'inn' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (Branch $root) => $root->inn,
            ],
            'created_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Branch $root) => $root->created_at->timestamp,
            ],
            'updated_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Branch $root) => $root->updated_at->timestamp,
            ],
            'deleted_at' => [
                'type' => Type::int(),
                'resolve' => fn (Branch $root) => $root->deleted_at?->timestamp,
            ],
        ];
    }
}
