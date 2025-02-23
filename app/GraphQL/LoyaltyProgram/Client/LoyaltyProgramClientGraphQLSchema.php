<?php

namespace App\GraphQL\LoyaltyProgram\Client;

use App\GraphQL\Ecommerce\Admin\Queries\SingleUserGraphQLQuery;
use App\GraphQL\LoyaltyProgram\Client\Types\UserGraphQLType;
use Rebing\GraphQL\Support\Contracts\ConfigConvertible;

class LoyaltyProgramClientGraphQLSchema implements ConfigConvertible
{
    public function toConfig(): array
    {
        return [
            'mutation' => [
            ],
            'query' => [
                SingleUserGraphQLQuery::class,
            ],
            'types' => [
                UserGraphQLType::class,
            ],
            'middleware' => ['auth:company-admin', 'is_soft_delete_token', 'tenant_logging'],
        ];
    }
}
