<?php

namespace App\GraphQL\Ecommerce\Common\Mutations;

use App\GraphQL\Scalars\BigInteger;
use App\Models\Company\Ecommerce\User;
use App\Rules\PhoneRule;
use App\Services\ConfirmationService;
use GraphQL\Type\Definition\Type;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Str;
use Rebing\GraphQL\Error\AuthorizationError;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class LoginGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'ecommerceLogin',
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
     * @throws AuthorizationError
     */
    public function resolve($root, array $args): string
    {
        $confirm = $this->confirmationService->confirm($args['phone'], $args['code']);
        if (! $confirm) {
            throw new AuthenticationException('Phone or code is wrong');
        }
        if (! request()->hasHeader('Device-Name') || ! request()->hasHeader('Device-Type') || ! request()->hasHeader('Device-OS')) {
            throw new AuthorizationError('Phone or code is wrong');
        }
        $user = User::query()->firstOrCreate(['phone' => $args['phone']]);

        return $user->createTokenWithData(
            Str::substr(request()->header('Device-Name'), 0, 255),
            [
                'device_type' => Str::substr(request()->header('Device-Type'), 0, 32),
                'device_os' => Str::substr(request()->header('Device-OS'), 0, 32),
                'device_ip' => request()->ip(),
            ]
        );

    }
}
