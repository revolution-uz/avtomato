<?php

namespace App\GraphQL\Management\Types;

use App\Models\Company\Attachment;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class AttachmentGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'AttachmentGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
        'description' => 'Attachment GraphQL Type',
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'ID of attachment',
                'resolve' => function (Attachment $root) {
                    return $root->id;
                },
            ],
            'type' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'Attachment type',
                'resolve' => function (Attachment $root) {
                    return $root->type->value;
                },
            ],
            'url' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Attachment url',
                'resolve' => function (Attachment $root) {
                    return $root->id > 0 ? $root->url : $root->path;
                },
            ],
            'name' => [
                'type' => Type::string(),
                'description' => 'Attachment name',
                'resolve' => function (Attachment $root) {
                    return $root->name;
                },
            ],
            'original_name' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Attachment original name',
                'resolve' => function (Attachment $root) {
                    return $root->original_name;
                },
            ],
            'mime' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Attachment mime type',
                'resolve' => function (Attachment $root) {
                    return $root->mime;
                },
            ],
            'created_at' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'Attachment created timestamp',
                'resolve' => function (Attachment $root) {
                    return $root->created_at->timestamp;
                },
            ],
            'updated_at' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'Attachment updated timestamp',
                'resolve' => function (Attachment $root) {
                    return $root->updated_at->timestamp;
                },
            ],
            'deleted_at' => [
                'type' => Type::int(),
                'description' => 'Attachment deleted timestamp',
                'resolve' => function (Attachment $root) {
                    return $root->deleted_at?->timestamp;
                },
            ],
        ];
    }
}
