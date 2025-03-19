<?php

namespace App\GraphQL\LoyaltyProgram\Admin\Types;

use App\GraphQL\Management\Types\AttachmentGraphQLType;
use App\Models\Company\CompanyAdmin;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class AdminGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'AdminGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (CompanyAdmin $root) => $root->id,
            ],
            'username' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (CompanyAdmin $root) => $root->username,
            ],
            'first_name' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (CompanyAdmin $root) => $root->first_name,
            ],
            'last_name' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (CompanyAdmin $root) => $root->last_name,
            ],
            'phone' => [
                'type' => Type::string(),
                'resolve' => fn (CompanyAdmin $root) => $root->phones()->latest()->first()->phone ?? null,
            ],
            'status' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (CompanyAdmin $root) => $root->status->value,
            ],
            'role_id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (CompanyAdmin $root) => $root->role_id,
            ],
            'created_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (CompanyAdmin $root) => $root->created_at->timestamp,
            ],
            'updated_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (CompanyAdmin $root) => $root->updated_at->timestamp,
            ],
            'deleted_at' => [
                'type' => Type::int(),
                'resolve' => fn (CompanyAdmin $root) => $root->deleted_at?->timestamp,
            ],
            'avatar' => [
                'type' => GraphQL::type(AttachmentGraphQLType::GRAPHQL_TYPE_NAME),
                'resolve' => fn (CompanyAdmin $root) => $root->avatar,
            ],
            'role' => [
                'type' => GraphQL::type(RoleGraphQLType::GRAPHQL_TYPE_NAME),
                'resolve' => fn (CompanyAdmin $root) => $root->role,
            ],
        ];
    }
}
