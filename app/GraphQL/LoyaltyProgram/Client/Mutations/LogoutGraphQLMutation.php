<?php

namespace App\GraphQL\LoyaltyProgram\Client\Mutations;

use App\Models\Company\PersonalAccessToken;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Error\AuthorizationError;
use Rebing\GraphQL\Support\Mutation;

class LogoutGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'logout',
    ];

    public function type(): Type
    {
        return Type::nonNull(Type::boolean());
    }

    public function args(): array
    {
        return [];
    }

    /**
     * @throws AuthorizationError
     */
    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        /** @var PersonalAccessToken $personalAccessToken */
        $personalAccessToken = auth('loyalty-program-client')->user()->currentAccessToken();
        $personalAccessToken->revokeAccessToken();

        return true;
    }
}
