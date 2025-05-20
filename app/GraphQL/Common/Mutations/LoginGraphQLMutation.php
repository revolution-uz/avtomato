<?php

namespace App\GraphQL\Common\Mutations;

use App\Models\Company\CompanyAdmin;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Rebing\GraphQL\Error\AuthorizationError;
use Rebing\GraphQL\Support\Mutation;

class LoginGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'login',
    ];

    public function type(): Type
    {
        return Type::nonNull(Type::string());
    }

    public function args(): array
    {
        return [
            'username' => [
                'type' => Type::nonNull(Type::string()),
            ],
            'password' => [
                'type' => Type::nonNull(Type::string()),
            ],
        ];
    }

    /**
     * @throws AuthorizationError
     */
    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        /** @var CompanyAdmin|null $admin */
        $admin = CompanyAdmin::query()->where('username', $args['username'])->first();

        if (! $admin || ! Hash::check($args['password'], $admin->password)) {
            throw ValidationException::withMessages(['username' => __('validation.invalid_credentials')]);
        }

        if (! request()->hasHeader('Device-Name') || ! request()->hasHeader('Device-Type') || ! request()->hasHeader('Device-OS')) {
            throw ValidationException::withMessages(['username' => __('validation.invalid_credentials')]);
        }

        return $admin->createTokenWithData(
            Str::substr(request()->header('Device-Name'), 0, 255),
            [
                'device_type' => Str::substr(request()->header('Device-Type'), 0, 32),
                'device_os' => Str::substr(request()->header('Device-OS'), 0, 32),
                'device_ip' => request()->ip(),
            ]
        );
    }
}
