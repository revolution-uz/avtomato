<?php

namespace App\GraphQL\Management\Types;

use App\Models\Company\SmsMessage;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class SmsMessageGraphQLType extends GraphQLType
{
    public const string GRAPHQL_TYPE_NAME = 'SmsMessageGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (SmsMessage $root) => $root->id,
            ],
            'phone' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (SmsMessage $root) => $root->phone,
            ],
            'message' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (SmsMessage $root) => $root->message,
            ],
            'status' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (SmsMessage $root) => $root->status->value,
            ],
            'ext_id' => [
                'type' => Type::int(),
                'resolve' => fn (SmsMessage $root) => $root->ext_id,
            ],
            'created_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (SmsMessage $root) => $root->created_at->timestamp,
            ],
            'updated_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (SmsMessage $root) => $root->updated_at->timestamp,
            ],
        ];
    }
}
