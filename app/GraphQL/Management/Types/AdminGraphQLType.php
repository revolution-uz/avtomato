<?php

namespace App\GraphQL\Management\Types;

use App\Models\Company\CompanyAdmin;
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
                'description' => 'ID of admin',
                'resolve' => function (CompanyAdmin $root) {
                    return $root->id;
                },
            ],
            'username' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Username of admin',
                'resolve' => function (CompanyAdmin $root) {
                    return $root->username;
                },
            ],
            'first_name' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Firstname of admin',
                'resolve' => function (CompanyAdmin $root) {
                    return $root->first_name;
                },
            ],
            'last_name' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Lastname of admin',
                'resolve' => function (CompanyAdmin $root) {
                    return $root->last_name;
                },
            ],
            'phone' => [
                'type' => Type::string(),
                'description' => 'Last phone number of admin',
                'resolve' => function (CompanyAdmin $root) {
                    return $root->phones()->latest()->first()->phone ?? null;
                },
            ],
            'status' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'Status of admin',
                'resolve' => function (CompanyAdmin $root) {
                    return $root->status->value;
                },
            ],
            'avatar' => [
                'type' => GraphQL::type(AttachmentGraphQLType::GRAPHQL_TYPE_NAME),
                'description' => 'Avatar attachment of admin',
                'resolve' => function (CompanyAdmin $root) {
                    return $root->avatar;
                },
            ],
            'template_theme' => [
                'type' => GraphQL::type(TemplateThemeGraphQLType::GRAPHQL_TYPE_NAME),
                'description' => 'Theme data',
                'resolve' => function (CompanyAdmin $root) {
                    return $root->template_theme;
                },
                'is_relation' => false,
            ],
            'language' => [
                'type' => Type::string(),
                'description' => 'Language of admin',
                'resolve' => function (CompanyAdmin $root) {
                    return $root->language;
                },
            ],
            'role_id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'Role ID of admin',
                'resolve' => function (CompanyAdmin $root) {
                    return $root->role_id;
                },
            ],
            'role' => [
                'type' => GraphQL::type(RoleGraphQLType::GRAPHQL_TYPE_NAME),
                'description' => 'Role of admin',
                'resolve' => function (CompanyAdmin $root) {
                    return $root->role;
                },
            ],
            'created_at' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'Attachment created timestamp',
                'resolve' => function (CompanyAdmin $root) {
                    return $root->created_at->timestamp;
                },
            ],
            'updated_at' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'Attachment updated timestamp',
                'resolve' => function (CompanyAdmin $root) {
                    return $root->updated_at->timestamp;
                },
            ],
            'deleted_at' => [
                'type' => Type::int(),
                'description' => 'Attachment deleted timestamp',
                'resolve' => function (CompanyAdmin $root) {
                    return $root->deleted_at?->timestamp;
                },
            ],
        ];
    }
}
