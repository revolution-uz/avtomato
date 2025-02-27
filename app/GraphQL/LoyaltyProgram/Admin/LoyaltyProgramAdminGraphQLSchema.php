<?php

namespace App\GraphQL\LoyaltyProgram\Admin;

use App\GraphQL\Ecommerce\Admin\Queries\SingleUserGraphQLQuery;
use App\GraphQL\LoyaltyProgram\Admin\Mutations\CreateTariffGraphQLMutation;
use App\GraphQL\LoyaltyProgram\Admin\Mutations\DeleteTariffGraphQLMutation;
use App\GraphQL\LoyaltyProgram\Admin\Mutations\UpdateTariffGraphQLMutation;
use App\GraphQL\LoyaltyProgram\Admin\Queries\SingleTariffGraphQLQuery;
use App\GraphQL\LoyaltyProgram\Admin\Queries\TariffsGraphQLQuery;
use App\GraphQL\LoyaltyProgram\Admin\Queries\UsersGraphQLQuery;
use App\GraphQL\LoyaltyProgram\Admin\Types\TariffGraphQLType;
use App\GraphQL\LoyaltyProgram\Admin\Types\TariffInputGraphQLType;
use App\GraphQL\LoyaltyProgram\Client\Types\UserGraphQLType;
use Rebing\GraphQL\Support\Contracts\ConfigConvertible;

class LoyaltyProgramAdminGraphQLSchema implements ConfigConvertible
{
    public function toConfig(): array
    {
        return [
            'mutation' => [
                CreateTariffGraphQLMutation::class,
                UpdateTariffGraphQLMutation::class,
                DeleteTariffGraphQLMutation::class,
            ],
            'query' => [
                SingleUserGraphQLQuery::class,
                UsersGraphQLQuery::class,
                TariffsGraphQLQuery::class,
                SingleTariffGraphQLQuery::class,
            ],
            'types' => [
                UserGraphQLType::class,
                TariffGraphQLType::class,
                TariffInputGraphQLType::class,
            ],
            'middleware' => ['auth:company-admin', 'is_soft_delete_token', 'loyalty_program', 'tenant_logging'],
        ];
    }
}
