<?php

namespace App\GraphQL\Ecommerce\Client\Mutations;

use App\Enums\Company\OrderDeliveryTypeEnum;
use App\Enums\Company\OrderPaymentMethodEnum;
use App\GraphQL\Ecommerce\Admin\Types\OrderGraphQLType;
use App\GraphQL\Ecommerce\Client\Types\UserCartInputGraphQLType;
use App\Models\Company\Address;
use App\Models\Company\Ecommerce\DeliveryPoint;
use App\Models\Company\Ecommerce\Order;
use App\Models\Company\Ecommerce\ShippingPrice;
use App\Services\Ecommerce\OrderService;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

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
            'delivery_type' => [
                'name' => 'delivery_type',
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    Rule::in(OrderDeliveryTypeEnum::values()),
                ],
            ],
            'payment_method' => [
                'name' => 'payment_method',
                'type' => Type::nonNull(Type::int()),
            ],
            'address_id' => [
                'name' => 'address_id',
                'type' => Type::int(),
                'rules' => [
                    'nullable',
                    'required_if:delivery_type,' . OrderDeliveryTypeEnum::DELIVERY_TO_ADDRESS->value,
                    function ($attribute, $value, $fail) {
                        if (! $address = Address::query()->find($value)) {
                            $fail(__('validation.exists', ['attribute' => 'address id']));
                        } elseif (
                            ShippingPrice::query()
                                ->where('region_id', $address->region_id)
                                ->where('district_id', $address->district_id)
                                ->where(fn ($q) => $q
                                    ->where('mahalla_id', $address->mahalla_id)
                                    ->orWhereNull('mahalla_id'))
                                ->doesntExist()
                        ) {
                            $fail('Delivery service to the specified address is currently unavailable');
                        }
                    },
                ],
            ],
            'delivery_point_id' => [
                'name' => 'delivery_point_id',
                'type' => Type::int(),
                'rules' => [
                    'nullable',
                    'required_if:delivery_type,' . OrderDeliveryTypeEnum::SELF_PICKUP->value,
                    Rule::exists(DeliveryPoint::class, 'id')->whereNull('deleted_at'),
                ],
            ],
            'installment_id' => [
                'name' => 'installment_id',
                'type' => Type::int(),
                'rules' => [
                    'nullable',
                    'required_if:payment_method,' . OrderPaymentMethodEnum::PAY_BY_INSTALLMENT->value,
                    'exists:installments,id',
                ],
            ],
            'cart' => [
                'name' => 'cart',
                'type' => Type::nonNull(Type::listOf(Type::nonNull(GraphQL::type(UserCartInputGraphQLType::GRAPHQL_TYPE_NAME)))),
            ],
        ];
    }

    public function resolve($root, $args): Order
    {
        $authUser = auth('ecommerce-client')->user();

        if (! $authUser->customer_id) {
            throw new Error('Please create a customer first');
        }

        return $this->orderService->createOrder($authUser, $args);
    }

    protected function rules(array $args = []): array
    {
        return [
            'payment_method' => [
                'required',
                Rule::in(OrderPaymentMethodEnum::values()),
                function ($attribute, $value, $fail) use ($args) {
                    if (
                        in_array($value, OrderPaymentMethodEnum::payAfterDeliveryMethods()) &&
                        (
                            $args['delivery_type'] === OrderDeliveryTypeEnum::DELIVERY_TO_ADDRESS->value ||
                            DeliveryPoint::query()->whereNotNull('branch_id')->where('id', $args['delivery_point_id'])->doesntExist()
                        )
                    ) {
                        $fail('This payment method is not allowed for the selected delivery type');
                    }
                },
            ],
        ];
    }
}
