<?php

namespace App\GraphQL\Ecommerce\Types;

use App\Models\Company\Ecommerce\EcommerceImage;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class EcommerceImageGraphQLType extends GraphQLType
{
    public const string GRAPHQL_TYPE_NAME = 'EcommerceImageGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'ID of image',
                'resolve' => function (EcommerceImage $root) {
                    return $root->id;
                },
            ],
            'url' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Image url',
                'resolve' => function (EcommerceImage $root) {
                    return $root->id ? $root->url : $root->path;
                },
            ],
            'original_name' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Image original name',
                'resolve' => function (EcommerceImage $root) {
                    return $root->original_name;
                },
            ],
            'mime' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Image mime type',
                'resolve' => function (EcommerceImage $root) {
                    return $root->mime;
                },
            ],
            'created_at' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'Image created timestamp',
                'resolve' => function (EcommerceImage $root) {
                    return $root->created_at->timestamp;
                },
            ],
            'updated_at' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'Image updated timestamp',
                'resolve' => function (EcommerceImage $root) {
                    return $root->updated_at->timestamp;
                },
            ],
            'deleted_at' => [
                'type' => Type::int(),
                'description' => 'Image deleted timestamp',
                'resolve' => function (EcommerceImage $root) {
                    return $root->deleted_at?->timestamp;
                },
            ],
        ];
    }
}
