<?php

namespace App\GraphQL\Ecommerce\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\InputType;

class OrderItemInputGraphQLType extends InputType
{
    public const string GRAPHQL_TYPE_NAME = 'OrderItemInputGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'product_id' => [
                'name' => 'product_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    'exists:e_commerce_products,id',
                ],
            ],
            'order_id' => [
                'name' => 'order_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    'exists:e_commerce_orders,id',
                ],
            ],
            'price' => [
                'name' => 'price',
                'type' => Type::nonNull(Type::float()),
                'rules' => [
                    'required',
                ],
            ],
            'quantity' => [
                'name' => 'quantity',
                'type' => Type::nonNull(Type::float()),
                'rules' => [
                    'required',
                ],
            ],
        ];
    }
}
