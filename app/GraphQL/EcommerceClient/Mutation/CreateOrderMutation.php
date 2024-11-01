<?php

namespace App\GraphQL\EcommerceClient\Mutation;

use App\Exceptions\GeneralException;
use App\Models\Company\Ecommerce\Order;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Mutation;

class CreateOrderMutation extends Mutation
{
    protected $attributes = [
        'name' => 'CreateOrder',
    ];

    public function type(): Type
    {
        return Type::nonNull(Type::boolean());
    }

    public function args(): array
    {
        return [
            'user_id' => [
                'name' => 'user_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    'exists:e_commerce_users,id',
                ],
            ],
            'address_id' => [
                'name' => 'address_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    'exists:addresses,id',
                ],
            ],
            'status_id' => [
                'name' => 'status_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    'exists:e_commerce_order_statuses,id',
                ],
            ],
            'installment_id' => [
                'name' => 'installment_id',
                'type' => Type::int(),
                'rules' => [
                    'nullable',
                    'exists:installments,id',
                ],
            ],
        ];
    }

    /**
     * @throws GeneralException
     */
    public function resolve($root, $args)
    {
        $authUser = auth('ecommerce-client')->user();
        if (is_null($authUser->customer_id)) {
            throw new GeneralException('');
        }
        $order = new Order;
        $order->user_id = $args['user_id'];
        $order->address_id = $args['address_id'];
        $order->status_id = $args['status_id'];
        $order->installment_id = $args['installment_id'];
        $order->save();
    }
}
