<?php

namespace App\GraphQL\LoyaltyProgram\Common\Types;

use App\GraphQL\GraphQLTypeHelper;
use App\Models\Company\LoyaltyProgram\OnboardingImage;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class OnboardingImageGraphQLType extends GraphQLType
{
    use GraphQLTypeHelper;

    public const string GRAPHQL_TYPE_NAME = 'OnboardingImageGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
        'model' => OnboardingImage::class,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (OnboardingImage $root) => $root->id,
            ],
            'image_url' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (OnboardingImage $root) => $root->image_url,
            ],
            'order' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (OnboardingImage $root) => $root->order,
            ],
            'is_active' => [
                'type' => Type::nonNull(Type::boolean()),
                'resolve' => fn (OnboardingImage $root) => $root->is_active,
            ],
        ];
    }
}
