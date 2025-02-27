<?php

namespace App\GraphQL\LoyaltyProgram\Common;

use App\GraphQL\LoyaltyProgram\Common\Mutations\CanLoginGraphQLMutation;
use App\GraphQL\LoyaltyProgram\Common\Mutations\LoginGraphQLMutation;
use App\GraphQL\LoyaltyProgram\Common\Mutations\SendOtpGraphQLMutation;
use App\GraphQL\LoyaltyProgram\Common\Queries\SingleTariffGraphQLQuery;
use App\GraphQL\LoyaltyProgram\Common\Queries\TariffsGraphQLQuery;
use App\GraphQL\LoyaltyProgram\Common\Types\TariffGraphQLType;
use Rebing\GraphQL\Support\Contracts\ConfigConvertible;

class LoyaltyProgramCommonGraphQLSchema implements ConfigConvertible
{
    public function toConfig(): array
    {
        return [
            'query' => [
                TariffsGraphQLQuery::class,
                SingleTariffGraphQLQuery::class,
            ],
            'mutation' => [
                CanLoginGraphQLMutation::class,
                SendOtpGraphQLMutation::class,
                LoginGraphQLMutation::class,
            ],
            'types' => [
                TariffGraphQLType::class,
            ],
            'middleware' => ['optional_auth:sanctum', 'loyalty_program'],
        ];
    }
}
