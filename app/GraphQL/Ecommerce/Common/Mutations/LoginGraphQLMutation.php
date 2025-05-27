<?php

namespace App\GraphQL\Ecommerce\Common\Mutations;

use App\Enums\Company\CustomerTypeEnum;
use App\Models\Company\Ecommerce\User;
use App\Rules\PhoneRule;
use App\Services\ConfirmationService;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Str;
use Rebing\GraphQL\Support\Mutation;

class LoginGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'ecommerceLogin',
    ];

    public function __construct(private readonly ConfirmationService $confirmationService) {}

    public function type(): Type
    {
        return Type::nonNull(Type::string());
    }

    public function args(): array
    {
        return [
            'phone' => [
                'name' => 'phone',
                'type' => Type::nonNull(Type::string()),
                'rules' => ['required', 'digits:12', new PhoneRule],
            ],
            'code' => [
                'name' => 'code',
                'type' => Type::nonNull(Type::string()),
                'rules' => ['required', 'digits:6'],
            ],
        ];
    }

    /**
     * @throws Error
     */
    public function resolve($root, array $args): string
    {
        if (
            ! $this->confirmationService->confirm($args['phone'], $args['code'])
            || (! request()->hasHeader('Device-Name') || ! request()->hasHeader('Device-Type') || ! request()->hasHeader('Device-OS'))
        ) {
            throw new Error('Phone or code is wrong');
        }

        $user = User::query()->firstOrCreate(['phone' => $args['phone']]);

        if (! $user->customer) {
            $user->customer()->create(['type' => CustomerTypeEnum::ECOMMERCE]);
        }

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
