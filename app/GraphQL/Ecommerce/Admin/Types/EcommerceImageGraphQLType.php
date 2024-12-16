<?php

namespace App\GraphQL\Ecommerce\Admin\Types;

use App\GraphQL\GraphQLTypeHelper;
use App\Models\Company\Ecommerce\EcommerceImage;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class EcommerceImageGraphQLType extends GraphQLType
{
    use GraphQLTypeHelper;

    public const string GRAPHQL_TYPE_NAME = 'EcommerceImageGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (EcommerceImage $root) => $root->id,
            ],
            'url' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (EcommerceImage $root) => $root->url,
            ],
            'original_name' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (EcommerceImage $root) => $root->original_name,
            ],
            'mime' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (EcommerceImage $root) => $root->mime,
            ],
            'order' => [
                'type' => Type::int(),
                'resolve' => fn (EcommerceImage $root) => $root->pivot?->order,
            ],
            'created_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (EcommerceImage $root) => $root->created_at->timestamp,
            ],
            'updated_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (EcommerceImage $root) => $root->updated_at->timestamp,
            ],
            'deleted_at' => [
                'type' => Type::int(),
                'resolve' => fn (EcommerceImage $root) => $root->deleted_at?->timestamp,
            ],
        ];
    }
}
