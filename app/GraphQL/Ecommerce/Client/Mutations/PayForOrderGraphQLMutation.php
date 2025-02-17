<?php

namespace App\GraphQL\Ecommerce\Client\Mutations;

use App\Exceptions\GeneralException;
use App\GraphQL\Ecommerce\Client\Types\PaymentGraphQLType;
use App\GraphQL\Scalars\BigInteger;
use App\Models\Company\Ecommerce\Order;
use App\Models\Company\Ecommerce\Payment;
use App\Services\Ecommerce\OrderService;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class PayForOrderGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'payForOrder',
    ];

    public function __construct(private readonly OrderService $_orderService) {}

    public function args(): array
    {
        return [
            'number' => [
                'name' => 'number',
                'type' => Type::nonNull(GraphQL::type(BigInteger::GRAPHQL_TYPE_NAME)),
                'rules' => 'required|digits:16',
            ],
            'expiry' => [
                'name' => 'expiry',
                'type' => Type::nonNull(Type::string()),
                'rules' => 'required|string|digits:4|date_format:ym',
            ],
            'order_id' => [
                'name' => 'order_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    'int',
                    Rule::exists(Order::class, 'id')->whereNull('deleted_at'),
                ],
            ],
        ];
    }

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(PaymentGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function resolve(array $args): Payment
    {
        try {
            return $this->_orderService->payForOrder(Order::query()->find($args['order_id']), $args);

        } catch (GeneralException $e) {
            throw new Error($e->getMessage());
        }
    }
}
