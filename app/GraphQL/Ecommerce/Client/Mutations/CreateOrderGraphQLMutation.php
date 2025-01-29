<?php

namespace App\GraphQL\Ecommerce\Client\Mutations;

use App\Exceptions\GeneralException;
use App\GraphQL\Ecommerce\Admin\Types\OrderGraphQLType;
use App\GraphQL\Ecommerce\Client\Types\UserCartInputGraphQLType;
use App\Models\Company\Address;
use App\Models\Company\Ecommerce\DeliveryPoint;
use App\Services\Ecommerce\OrderService;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use Throwable;

class CreateOrderGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'createOrder',
    ];

    public function __construct(readonly OrderService $orderService) {}

    public function type(): Type
    {
        return GraphQL::type(OrderGraphQLType::GRAPHQL_TYPE_NAME);
    }

    public function args(): array
    {
        return [
            'address_id' => [
                'name' => 'address_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    Rule::exists(Address::class, 'id')->whereNull('deleted_at'),
                ],
            ],
            'delivery_point_id' => [
                'name' => 'delivery_point_id',
                'type' => Type::int(),
                'rules' => [
                    'nullable',
                    Rule::exists(DeliveryPoint::class, 'id')->whereNull('deleted_at'),
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
            'cart' => [
                'name' => 'cart',
                'type' => Type::listOf(Type::nonNull(GraphQL::type(UserCartInputGraphQLType::GRAPHQL_TYPE_NAME))),
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

        return $this->orderService->createOrder($authUser, $args['cart'], $args['address_id'], $args['installment_id'] ?? null);
    }
}
