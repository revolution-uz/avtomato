<?php

namespace App\GraphQL\LoyaltyProgram\Admin;

use App\GraphQL\Ecommerce\Admin\Queries\SingleUserGraphQLQuery;
use App\GraphQL\LoyaltyProgram\Admin\Queries\UsersGraphQLQuery;
use App\GraphQL\LoyaltyProgram\Client\Types\UserGraphQLType;
use Rebing\GraphQL\Support\Contracts\ConfigConvertible;

class LoyaltyProgramAdminGraphQLSchema implements ConfigConvertible
{
    public function toConfig(): array
    {
        return [
            'mutation' => [
            ],
            'query' => [
                SingleUserGraphQLQuery::class,
                UsersGraphQLQuery::class,
            ],
            'types' => [
                UserGraphQLType::class,
            ],
            'middleware' => ['auth:company-admin', 'is_soft_delete_token', 'loyalty', 'tenant_logging'],
        ];
    }
}
