<?php

namespace App\GraphQL\LoyaltyProgram\Common;

use App\GraphQL\LoyaltyProgram\Common\Mutations\CanLoginGraphQLMutation;
use App\GraphQL\LoyaltyProgram\Common\Mutations\LoginGraphQLMutation;
use App\GraphQL\LoyaltyProgram\Common\Mutations\SendOtpGraphQLMutation;
use Rebing\GraphQL\Support\Contracts\ConfigConvertible;

class LoyaltyProgramCommonGraphQLSchema implements ConfigConvertible
{
    public function toConfig(): array
    {
        return [
            'query' => [
            ],
            'mutation' => [
                CanLoginGraphQLMutation::class,
                SendOtpGraphQLMutation::class,
                LoginGraphQLMutation::class,
            ],
            'types' => [
            ],
            'middleware' => ['optional_auth:sanctum', 'loyalty_program'],
        ];
    }
}
