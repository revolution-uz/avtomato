<?php

namespace App\GraphQL\LoyaltyProgram\Client;

use App\GraphQL\LoyaltyProgram\Client\Mutations\LogoutGraphQLMutation;
use App\GraphQL\LoyaltyProgram\Client\Mutations\ProfileUpdateGraphQLMutation;
use App\GraphQL\LoyaltyProgram\Client\Queries\ProfileMeGraphQLQuery;
use App\GraphQL\LoyaltyProgram\Client\Types\UserGraphQLType;
use Rebing\GraphQL\Support\Contracts\ConfigConvertible;

class LoyaltyProgramClientGraphQLSchema implements ConfigConvertible
{
    public function toConfig(): array
    {
        return [
            'mutation' => [
                ProfileUpdateGraphQLMutation::class,
                LogoutGraphQLMutation::class,
            ],
            'query' => [
                ProfileMeGraphQLQuery::class,
            ],
            'types' => [
                UserGraphQLType::class,
            ],
            'middleware' => ['auth:loyalty-program-client', 'is_soft_delete_loyalty_client_token', 'loyalty'],
        ];
    }
}
