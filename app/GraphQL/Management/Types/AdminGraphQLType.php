<?php

namespace App\GraphQL\Management\Types;

use App\Models\Company\User;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class AdminGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'AdminGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
        'description' => 'Profile Me GraphQL Type',
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'ID of user',
                'resolve' => function (User $root) {
                    return $root->id;
                },
            ],
            'username' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Username of user',
                'resolve' => function (User $root) {
                    return $root->username;
                },
            ],
            'first_name' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Firstname of user',
                'resolve' => function (User $root) {
                    return $root->first_name;
                },
            ],
            'last_name' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Lastname of user',
                'resolve' => function (User $root) {
                    return $root->last_name;
                },
            ],
            'phone' => [
                'type' => Type::string(),
                'description' => 'Last phone number of user',
                'resolve' => function (User $root) {
                    return $root->phones()->latest()->first()->phone ?? null;
                },
            ],
            'status' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'Status of user',
                'resolve' => function (User $root) {
                    return $root->status->value;
                },
            ],
            'avatar' => [
                'type' => GraphQL::type(AttachmentGraphQLType::GRAPHQL_TYPE_NAME),
                'description' => 'Avatar attachment of user',
                'resolve' => function (User $root) {
                    return $root->avatar;
                },
            ],
            'template_theme' => [
                'type' => GraphQL::type(TemplateThemeGraphQLType::GRAPHQL_TYPE_NAME),
                'description' => 'Theme data',
                'resolve' => function (User $root) {
                    return $root->template_theme;
                },
                'is_relation' => false,
            ],
            'language' => [
                'type' => Type::string(),
                'description' => 'Language of user',
                'resolve' => function (User $root) {
                    return $root->language;
                },
            ],
            'role_id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'Role ID of user',
                'resolve' => function (User $root) {
                    return $root->role_id;
                },
            ],
            'role' => [
                'type' => GraphQL::type(RoleGraphQLType::GRAPHQL_TYPE_NAME),
                'description' => 'Role of user',
                'resolve' => function (User $root) {
                    return $root->role;
                },
            ],
            'created_at' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'Attachment created timestamp',
                'resolve' => function (User $root) {
                    return $root->created_at->timestamp;
                },
            ],
            'updated_at' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'Attachment updated timestamp',
                'resolve' => function (User $root) {
                    return $root->updated_at->timestamp;
                },
            ],
            'deleted_at' => [
                'type' => Type::int(),
                'description' => 'Attachment deleted timestamp',
                'resolve' => function (User $root) {
                    return $root->deleted_at?->timestamp;
                },
            ],
        ];
    }
}
