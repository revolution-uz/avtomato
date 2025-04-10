<?php

namespace App\GraphQL\LoyaltyProgram\Admin\Types;

use App\GraphQL\LoyaltyProgram\Admin\Mutations\LoyaltyProgramImageGraphQLType;
use App\Models\Company\LoyaltyProgram\Notification;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class NotificationGraphQLType extends GraphQLType
{
    public const string GRAPHQL_TYPE_NAME = 'NotificationGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Notification $root) => $root->id,
            ],
            'title_uz' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (Notification $root) => $root->title_uz,
            ],
            'title_ru' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (Notification $root) => $root->title_ru,
            ],
            'body_uz' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (Notification $root) => $root->body_uz,
            ],
            'body_ru' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (Notification $root) => $root->body_ru,
            ],
            'is_sent' => [
                'type' => Type::nonNull(Type::boolean()),
                'resolve' => fn (Notification $root) => $root->is_sent,
            ],
            'image_uz_id' => [
                'type' => Type::int(),
                'resolve' => fn (Notification $root) => $root->image_uz_id,
            ],
            'image_ru_id' => [
                'type' => Type::int(),
                'resolve' => fn (Notification $root) => $root->image_ru_id,
            ],
            'created_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Notification $root) => $root->created_at->timestamp,
            ],
            'updated_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Notification $root) => $root->updated_at->timestamp,
            ],
            'image' => [
                'type' => GraphQL::type(LoyaltyProgramImageGraphQLType::GRAPHQL_TYPE_NAME),
                'resolve' => fn (Notification $root) => $root->image,
            ],
            'image_uz' => [
                'type' => GraphQL::type(LoyaltyProgramImageGraphQLType::GRAPHQL_TYPE_NAME),
                'resolve' => fn (Notification $root) => $root->imageUz,
            ],
            'image_ru' => [
                'type' => GraphQL::type(LoyaltyProgramImageGraphQLType::GRAPHQL_TYPE_NAME),
                'resolve' => fn (Notification $root) => $root->imageRu,
            ],
        ];
    }
}
