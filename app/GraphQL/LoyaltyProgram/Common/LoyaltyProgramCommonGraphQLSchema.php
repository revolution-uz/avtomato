<?php

namespace App\GraphQL\LoyaltyProgram\Common;

use App\GraphQL\LoyaltyProgram\Common\Mutations\CanLoginGraphQLMutation;
use App\GraphQL\LoyaltyProgram\Common\Mutations\LoginGraphQLMutation;
use App\GraphQL\LoyaltyProgram\Common\Mutations\SendOtpGraphQLMutation;
use App\GraphQL\LoyaltyProgram\Common\Queries\SettingsGraphQLQuery;
use App\GraphQL\LoyaltyProgram\Common\Types\SettingsGraphQLType;
use Rebing\GraphQL\Support\Contracts\ConfigConvertible;

class LoyaltyProgramCommonGraphQLSchema implements ConfigConvertible
{
    public function toConfig(): array
    {
        return [
            'query' => [
                SettingsGraphQLQuery::class,
            ],
            'mutation' => [
                CanLoginGraphQLMutation::class,
                SendOtpGraphQLMutation::class,
                LoginGraphQLMutation::class,
            ],
            'types' => [
                SettingsGraphQLType::class,
            ],
            'middleware' => ['optional_auth:sanctum', 'loyalty_program'],
        ];
    }
}
