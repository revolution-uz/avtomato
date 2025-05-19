<?php

namespace App\GraphQL\LoyaltyProgram\Admin\Mutations;

use App\GraphQL\GraphQLTypeHelper;
use App\Models\Company\LoyaltyProgram\LoyaltyProgramImage;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class LoyaltyProgramImageGraphQLType extends GraphQLType
{
    use GraphQLTypeHelper;

    public const string GRAPHQL_TYPE_NAME = 'LoyaltyProgramImageGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (LoyaltyProgramImage $root) => $root->id,
            ],
            'url' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (LoyaltyProgramImage $root) => $root->url,
            ],
            'original_name' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (LoyaltyProgramImage $root) => $root->original_name,
            ],
            'mime' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (LoyaltyProgramImage $root) => $root->mime,
            ],
            'created_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (LoyaltyProgramImage $root) => $root->created_at->timestamp,
            ],
            'updated_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (LoyaltyProgramImage $root) => $root->updated_at->timestamp,
            ],
        ];
    }
}
