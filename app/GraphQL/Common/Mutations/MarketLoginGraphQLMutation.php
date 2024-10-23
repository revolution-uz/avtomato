<?php

namespace App\GraphQL\Common\Mutations;

use App\Enums\Company\AllowedEnum;
use App\Enums\Company\CustomerTypeEnum;
use App\GraphQL\Scalars\BigInteger;
use App\Models\Company\Customer;
use App\Rules\PhoneRule;
use App\Services\ConfirmationService;
use GraphQL\Type\Definition\Type;
use Illuminate\Auth\AuthenticationException;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class MarketLoginGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'marketLogin',
    ];

    public function __construct(
        private readonly ConfirmationService $confirmationService,
    ) {}

    public function type(): Type
    {
        return Type::nonNull(Type::string());
    }

    public function args(): array
    {
        return [
            'phone' => [
                'name' => 'phone',
                'type' => Type::nonNull(GraphQL::type(BigInteger::GRAPHQL_TYPE_NAME)),
                'rules' => ['distinct', 'required', 'int', new PhoneRule],
            ],
            'code' => [
                'name' => 'code',
                'type' => Type::nonNull(Type::int()),
            ],
        ];
    }

    /**
     * @throws AuthenticationException
     */
    public function resolve($root, array $args): string
    {
        if ($args['phone'] === AllowedEnum::PHONE->value and (string) $args['code'] === AllowedEnum::OTP_CODE->value) {
            $customer = Customer::create(['type' => CustomerTypeEnum::MARKET]);

            return $customer->createToken(request()->ip())->plainTextToken;
        }
        $confirm = $this->confirmationService->confirm($args['phone'], $args['code']);
        if (! $confirm) {
            throw new AuthenticationException('Unauthenticated');
        }
        $customer = Customer::query()->whereHas('phones', function ($query) use ($args) {
            $query->where('phone', $args['phone']);
        })->firstOrFail();

        return $customer->createToken(request()->ip())->plainTextToken;

    }
}
