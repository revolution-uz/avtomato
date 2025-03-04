<?php

namespace App\GraphQL\LoyaltyProgram\Admin;

use App\GraphQL\Ecommerce\Admin\Queries\SingleUserGraphQLQuery;
use App\GraphQL\LoyaltyProgram\Admin\Mutations\CreateNotificationGraphQLMutation;
use App\GraphQL\LoyaltyProgram\Admin\Mutations\CreateTariffGraphQLMutation;
use App\GraphQL\LoyaltyProgram\Admin\Mutations\DeleteNotificationsGraphQLMutation;
use App\GraphQL\LoyaltyProgram\Admin\Mutations\DeleteTariffGraphQLMutation;
use App\GraphQL\LoyaltyProgram\Admin\Mutations\SendNotificationsGraphQLMutation;
use App\GraphQL\LoyaltyProgram\Admin\Mutations\UpdateNotificationGraphQLMutation;
use App\GraphQL\LoyaltyProgram\Admin\Mutations\UpdateTariffGraphQLMutation;
use App\GraphQL\LoyaltyProgram\Admin\Queries\NotificationsGraphQLQuery;
use App\GraphQL\LoyaltyProgram\Admin\Queries\SingleNotificationGraphQLQuery;
use App\GraphQL\LoyaltyProgram\Admin\Queries\SingleSubscriptionGraphQLQuery;
use App\GraphQL\LoyaltyProgram\Admin\Queries\SingleTariffGraphQLQuery;
use App\GraphQL\LoyaltyProgram\Admin\Queries\SubscriptionsGraphQLQuery;
use App\GraphQL\LoyaltyProgram\Admin\Queries\TariffsGraphQLQuery;
use App\GraphQL\LoyaltyProgram\Admin\Queries\UsersGraphQLQuery;
use App\GraphQL\LoyaltyProgram\Admin\Types\NotificationGraphQLType;
use App\GraphQL\LoyaltyProgram\Admin\Types\SubscriptionGraphQLType;
use App\GraphQL\LoyaltyProgram\Admin\Types\TariffGraphQLType;
use App\GraphQL\LoyaltyProgram\Admin\Types\TariffInputGraphQLType;
use App\GraphQL\LoyaltyProgram\Client\Types\CardGraphQLType;
use App\GraphQL\LoyaltyProgram\Client\Types\UserGraphQLType;
use Rebing\GraphQL\Support\Contracts\ConfigConvertible;

class LoyaltyProgramAdminGraphQLSchema implements ConfigConvertible
{
    public function toConfig(): array
    {
        return [
            'mutation' => [
                CreateTariffGraphQLMutation::class,
                CreateNotificationGraphQLMutation::class,
                UpdateTariffGraphQLMutation::class,
                UpdateNotificationGraphQLMutation::class,
                DeleteTariffGraphQLMutation::class,
                DeleteNotificationsGraphQLMutation::class,
                SendNotificationsGraphQLMutation::class,
            ],
            'query' => [
                SingleUserGraphQLQuery::class,
                UsersGraphQLQuery::class,
                TariffsGraphQLQuery::class,
                NotificationsGraphQLQuery::class,
                SingleTariffGraphQLQuery::class,
                SingleNotificationGraphQLQuery::class,
                SubscriptionsGraphQLQuery::class,
                SingleSubscriptionGraphQLQuery::class,
            ],
            'types' => [
                UserGraphQLType::class,
                TariffGraphQLType::class,
                TariffInputGraphQLType::class,
                NotificationGraphQLType::class,
                SubscriptionGraphQLType::class,
                CardGraphQLType::class,
            ],
            'middleware' => ['auth:company-admin', 'is_soft_delete_token', 'loyalty_program', 'tenant_logging'],
        ];
    }
}
