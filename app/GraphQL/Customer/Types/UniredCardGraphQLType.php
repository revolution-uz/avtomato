<?php

namespace App\GraphQL\Customer\Types;

use App\Models\CentralUniredCard;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class UniredCardGraphQLType extends GraphQLType
{
    public const string GRAPHQL_TYPE_NAME = 'UniredCardGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (CentralUniredCard $root) => $root->id,
            ],
            'customer_id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (CentralUniredCard $root) => $root->customer_id,
            ],
            'pinfl' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (CentralUniredCard $root) => $root->pinfl,
            ],
            'uuid' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (CentralUniredCard $root) => $root->uuid,
            ],
            'pan' => [
                'type' => Type::string(),
                'resolve' => fn (CentralUniredCard $root) => $root->pan,
            ],
            'phone' => [
                'type' => Type::string(),
                'resolve' => fn (CentralUniredCard $root) => $root->phone,
            ],
            'type' => [
                'type' => Type::int(),
                'resolve' => fn (CentralUniredCard $root) => $root->type?->value,
            ],
            'owner' => [
                'type' => Type::string(),
                'resolve' => fn (CentralUniredCard $root) => $root->owner,
            ],
            'auto' => [
                'type' => Type::boolean(),
                'resolve' => fn (CentralUniredCard $root) => $root->auto,
            ],
            'is_verified' => [
                'type' => Type::boolean(),
                'resolve' => fn (CentralUniredCard $root) => $root->is_verified,
            ],
            'is_blocked' => [
                'type' => Type::boolean(),
                'resolve' => fn (CentralUniredCard $root) => $root->is_blocked,
            ],
            'status' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (CentralUniredCard $root) => $root->status->value,
            ],
            'block_reason' => [
                'type' => Type::string(),
                'resolve' => fn (CentralUniredCard $root) => $root->block_reason,
            ],
            'blocked_at' => [
                'type' => Type::int(),
                'resolve' => fn (CentralUniredCard $root) => $root->blocked_at?->timestamp,
            ],
            'created_at' => [
                'type' => Type::int(),
                'resolve' => fn (CentralUniredCard $root) => $root->created_at->timestamp,
            ],
            'updated_at' => [
                'type' => Type::int(),
                'resolve' => fn (CentralUniredCard $root) => $root->updated_at->timestamp,
            ],
        ];
    }
}
