<?php

namespace App\GraphQL\EcommerceClient\Mutation;

use App\Enums\Company\OrderStatusEnum;
use App\Exceptions\GeneralException;
use App\GraphQL\Ecommerce\Types\OrderGraphQLType;
use App\GraphQL\Ecommerce\Types\OrderInputGraphQLType;
use App\Models\Company\Ecommerce\Order;
use App\Models\Company\Ecommerce\OrderStatus;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use Throwable;

class CreateOrderGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'createOrder',
    ];

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(OrderGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function args(): array
    {
        return [
            'address_id' => [
                'name' => 'address_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    'exists:addresses,id',
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
            'orderItems' => [
                'name' => 'orderItems',
                'type' => Type::listOf(Type::nonNull(GraphQL::type(OrderInputGraphQLType::GRAPHQL_TYPE_NAME))),
            ],
        ];
    }

    /**
     * @throws GeneralException
     * @throws Throwable
     */
    public function resolve($root, $args)
    {
        $authUser = auth('ecommerce-client')->user();
        if (is_null($authUser->customer_id)) {
            throw new GeneralException('Iltimos profilni to\'ldiring');
        }
        $order = new Order;
        $order->user_id = $authUser->id;
        $order->address_id = $args['address_id'];
        $order->status_id = OrderStatus::query()->where('name_en', OrderStatusEnum::CREATED)->first()->id;
        $order->installment_id = $args['installment_id'];
        $order->saveOrFail();
        foreach ($args['orderItems'] as $item) {
            $order->orderItems()->create($item);
        }

        return $order;
    }
}
