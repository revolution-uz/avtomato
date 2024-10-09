<?php

namespace App\GraphQL\Management\Types;

use App\Models\Company\PersonalAccessToken;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class DeviceGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'DeviceGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
        'description' => 'Device GraphQL Type',
        'model' => PersonalAccessToken::class, // to get sortable columns
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'ID of token',
                'resolve' => fn (PersonalAccessToken $root) => $root->id,
            ],
            'name' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Token name',
                'resolve' => fn (PersonalAccessToken $root) => $root->name,
            ],
            'device_ip' => [
                'type' => Type::string(),
                'description' => 'IP address',
                'resolve' => fn (PersonalAccessToken $root) => $root->device_ip,
            ],
            'device_type' => [
                'type' => Type::string(),
                'resolve' => fn (PersonalAccessToken $root) => $root->device_type,
            ],
            'device_os' => [
                'type' => Type::string(),
                'resolve' => fn (PersonalAccessToken $root) => $root->device_os,
            ],
            'created_at' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'Attachment created timestamp',
                'resolve' => fn (PersonalAccessToken $root) => $root->created_at->timestamp,
            ],
            'last_used_at' => [
                'type' => Type::int(),
                'resolve' => fn (PersonalAccessToken $root) => $root->last_used_at?->timestamp,
            ],
        ];
    }
}
