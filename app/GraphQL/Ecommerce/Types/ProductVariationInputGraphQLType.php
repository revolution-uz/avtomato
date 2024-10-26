<?php

namespace App\GraphQL\Ecommerce\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\InputType;

class ProductVariationInputGraphQLType extends InputType
{
    public const string GRAPHQL_TYPE_NAME = 'ProductVariationInputGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'product_id' => [
                'name' => 'product_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => ['required', 'exists:e_commerce_products,id'],
            ],
            'is_active' => [
                'name' => 'status',
                'type' => Type::nonNull(Type::boolean()),
                'rules' => ['required'],
            ],
        ];
    }
}
