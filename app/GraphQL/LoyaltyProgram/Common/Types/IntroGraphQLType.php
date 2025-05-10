<?php

namespace App\GraphQL\LoyaltyProgram\Common\Types;

use App\GraphQL\GraphQLTypeHelper;
use App\Models\Company\LoyaltyProgram\Intro;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class IntroGraphQLType extends GraphQLType
{
    use GraphQLTypeHelper;

    public const string GRAPHQL_TYPE_NAME = 'IntroGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
        'model' => Intro::class,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Intro $root) => $root->id,
            ],
            'image_url' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (Intro $root) => $root->image_url,
            ],
            'order' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Intro $root) => $root->order,
            ],
            'is_active' => [
                'type' => Type::nonNull(Type::boolean()),
                'resolve' => fn (Intro $root) => $root->is_active,
            ],
        ];
    }
}
