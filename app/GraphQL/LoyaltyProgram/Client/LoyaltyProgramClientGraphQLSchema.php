<?php

namespace App\GraphQL\LoyaltyProgram\Client;

use App\GraphQL\Ecommerce\Admin\Queries\SingleUserGraphQLQuery;
use App\GraphQL\LoyaltyProgram\Client\Mutations\LogoutGraphQLMutation;
use App\GraphQL\LoyaltyProgram\Client\Types\UserGraphQLType;
use Rebing\GraphQL\Support\Contracts\ConfigConvertible;

class LoyaltyProgramClientGraphQLSchema implements ConfigConvertible
{
    public function toConfig(): array
    {
        return [
            'mutation' => [
                LogoutGraphQLMutation::class,
            ],
            'query' => [
                SingleUserGraphQLQuery::class,
            ],
            'types' => [
                UserGraphQLType::class,
            ],
            'middleware' => ['auth:loyalty-program-client', 'is_soft_delete_client_token', 'loyalty-program'],
        ];
    }
}
