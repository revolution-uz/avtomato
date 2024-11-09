<?php

namespace App\GraphQL\EcommerceClient\Mutation;

use App\Enums\Company\OrderStatusEnum;
use App\Exceptions\GeneralException;
use App\GraphQL\Ecommerce\Types\OrderGraphQLType;
use App\Models\Company\Ecommerce\Order;
use App\Models\Company\Ecommerce\OrderStatus;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use Throwable;

class CreateOrderMutation extends Mutation
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
            'user_id' => [
                'name' => 'user_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    'exists:ecommerce_users,id',
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
     * @throws Throwable
     */
    public function resolve($root, $args)
    {
        $authUser = auth('ecommerce-client')->user();
        if (is_null($authUser->customer_id)) {
            throw new GeneralException('Iltimos profilni to\'ldiring');
        }
        $order = new Order;
        $order->user_id = $args['user_id'];
        $order->address_id = $args['address_id'];
        $order->status_id = OrderStatus::query()->where('name_en', OrderStatusEnum::CREATED)->first()->id;
        $order->installment_id = $args['installment_id'];
        $order->saveOrFail();

        return $order;
    }
}
