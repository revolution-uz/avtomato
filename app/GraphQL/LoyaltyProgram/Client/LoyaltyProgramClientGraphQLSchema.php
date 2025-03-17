<?php

namespace App\GraphQL\LoyaltyProgram\Client;

use App\GraphQL\Customer\Types\AttachCardDataGraphQLType;
use App\GraphQL\LoyaltyProgram\Admin\Types\SubscriptionGraphQLType;
use App\GraphQL\LoyaltyProgram\Admin\Types\TariffGraphQLType;
use App\GraphQL\LoyaltyProgram\Client\Mutations\AttachCardGraphQLMutation;
use App\GraphQL\LoyaltyProgram\Client\Mutations\CancelSubscriptionGraphQLMutation;
use App\GraphQL\LoyaltyProgram\Client\Mutations\ConfirmCardGraphQLMutation;
use App\GraphQL\LoyaltyProgram\Client\Mutations\CreateSubscriptionGraphQLMutation;
use App\GraphQL\LoyaltyProgram\Client\Mutations\DetachCardGraphQLMutation;
use App\GraphQL\LoyaltyProgram\Client\Mutations\LogoutGraphQLMutation;
use App\GraphQL\LoyaltyProgram\Client\Mutations\ProfileUpdateGraphQLMutation;
use App\GraphQL\LoyaltyProgram\Client\Queries\ProfileMeGraphQLQuery;
use App\GraphQL\LoyaltyProgram\Client\Types\AttachCardGraphQLType;
use App\GraphQL\LoyaltyProgram\Client\Types\CardGraphQLType;
use App\GraphQL\LoyaltyProgram\Client\Types\QrGraphQLType;
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
                CreateSubscriptionGraphQLMutation::class,
                CancelSubscriptionGraphQLMutation::class,
                AttachCardGraphQLMutation::class,
                ConfirmCardGraphQLMutation::class,
                DetachCardGraphQLMutation::class,
            ],
            'query' => [
                ProfileMeGraphQLQuery::class,
            ],
            'types' => [
                UserGraphQLType::class,
                SubscriptionGraphQLType::class,
                CardGraphQLType::class,
                TariffGraphQLType::class,
                AttachCardGraphQLType::class,
                CardGraphQLType::class,
                AttachCardDataGraphQLType::class,
                QrGraphQLType::class,
            ],
            'middleware' => ['auth:loyalty-program-client', 'is_soft_delete_loyalty_program_client_token', 'loyalty_program'],
        ];
    }
}
