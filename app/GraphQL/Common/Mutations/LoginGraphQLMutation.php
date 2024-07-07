<?php

namespace App\GraphQL\Common\Mutations;

use App\Models\Company\CompanyAdmin;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
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
                'name' => 'username',
                'type' => Type::nonNull(Type::string()),
            ],
            'password' => [
                'name' => 'password',
                'type' => Type::nonNull(Type::string()),
            ],
        ];
    }

    /**
     * @throws AuthorizationError
     */
    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        /** @var CompanyAdmin|null $user */
        $user = CompanyAdmin::query()->where('username', $args['username'])->first();
        if (! $user || ! Hash::check($args['password'], $user->password)) {
            throw new AuthorizationError('Login or password is wrong');
        }

        if (! request()->hasHeader('Device-Name') || ! request()->hasHeader('Device-Type') || ! request()->hasHeader('Device-OS')) {
            throw new AuthorizationError('Login or password is wrong');
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
