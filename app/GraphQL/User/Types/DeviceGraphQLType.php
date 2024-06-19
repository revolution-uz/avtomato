<?php

namespace App\GraphQL\User\Types;

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
                'resolve' => function (PersonalAccessToken $root) {
                    return $root->id;
                },
            ],
            'name' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Token name',
                'resolve' => function (PersonalAccessToken $root) {
                    return $root->name;
                },
            ],
            'device_ip' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'IP address',
                'resolve' => function (PersonalAccessToken $root) {
                    return $root->device_ip;
                },
            ],
            'device_type' => [
                'type' => Type::string(),
                'resolve' => function (PersonalAccessToken $root) {
                    return $root->device_type;
                },
            ],
            'device_os' => [
                'type' => Type::string(),
                'resolve' => function (PersonalAccessToken $root) {
                    return $root->device_os;
                },
            ],
            'created_at' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'Attachment created timestamp',
                'resolve' => function (PersonalAccessToken $root) {
                    return $root->created_at->timestamp;
                },
            ],
            'last_used_at' => [
                'type' => Type::int(),
                'resolve' => function (PersonalAccessToken $root) {
                    return $root->last_used_at?->timestamp;
                },
            ],
        ];
    }
}
