<?php

namespace App\GraphQL\Ecommerce\Client\Mutations;

use App\Exceptions\GeneralException;
use App\GraphQL\Ecommerce\Client\Types\PaymentGraphQLType;
use App\Models\Company\Ecommerce\Payment;
use App\Services\Ecommerce\OrderService;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class PayForOrderConfirmGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'payForOrderConfirm',
    ];

    public function __construct(private readonly OrderService $_orderService) {}

    public function args(): array
    {
        return [
            'payment_id' => [
                'name' => 'payment_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    'integer',
                    Rule::exists(Payment::class, 'id')->where('user_id', auth('ecommerce-client')->id()),
                ],
            ],
            'otp' => [
                'name' => 'otp',
                'type' => Type::nonNull(Type::string()),
                'rules' => 'required|string',
            ],
        ];
    }

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(PaymentGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        try {
            return $this->_orderService->payForOrderConfirm(Payment::query()->find($args['payment_id']), $args['otp']);

        } catch (GeneralException $e) {
            throw ValidationException::withMessages(['otp' => $e->getMessage()]);
        }
    }
}
