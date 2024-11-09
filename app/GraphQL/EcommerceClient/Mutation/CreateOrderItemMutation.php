<?php

namespace App\GraphQL\EcommerceClient\Mutation;

use App\GraphQL\Ecommerce\Types\OrderItemGraphQLType;
use App\Models\Company\Ecommerce\OrderItem;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use Throwable;

class CreateOrderItemMutation extends Mutation
{
    protected $attributes = [
        'name' => 'CreateOrderItem',
    ];

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(OrderItemGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function args(): array
    {
        return [
            'product_id' => [
                'name' => 'product_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    'exists:ecommerce_products,id',
                ],
            ],
            'order_id' => [
                'name' => 'order_id',
                'type' => Type::int(),
                'rules' => [
                    'nullable',
                    'exists:ecommerce_orders,id',
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

    /**
     * @throws Throwable
     */
    public function resolve($root, $args)
    {
        $orderItem = new OrderItem;
        $orderItem->product_id = $args['product_id'];
        $orderItem->order_id = $args['order_id'];
        $orderItem->price = $args['price'];
        $orderItem->quantity = $args['quantity'];
        $orderItem->saveOrFail();

        return $orderItem;
    }
}
