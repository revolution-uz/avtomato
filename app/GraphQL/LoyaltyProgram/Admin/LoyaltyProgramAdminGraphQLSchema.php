<?php

namespace App\GraphQL\LoyaltyProgram\Admin;

use App\GraphQL\LoyaltyProgram\Admin\Mutations\CreateNotificationGraphQLMutation;
use App\GraphQL\LoyaltyProgram\Admin\Mutations\CreateTariffGraphQLMutation;
use App\GraphQL\LoyaltyProgram\Admin\Mutations\DeleteNotificationsGraphQLMutation;
use App\GraphQL\LoyaltyProgram\Admin\Mutations\DeleteTariffGraphQLMutation;
use App\GraphQL\LoyaltyProgram\Admin\Mutations\SendNotificationsGraphQLMutation;
use App\GraphQL\LoyaltyProgram\Admin\Mutations\UpdateNotificationGraphQLMutation;
use App\GraphQL\LoyaltyProgram\Admin\Mutations\UpdateTariffGraphQLMutation;
use App\GraphQL\LoyaltyProgram\Admin\Queries\NotificationsGraphQLQuery;
use App\GraphQL\LoyaltyProgram\Admin\Queries\OrdersGraphQLQuery;
use App\GraphQL\LoyaltyProgram\Admin\Queries\SingleNotificationGraphQLQuery;
use App\GraphQL\LoyaltyProgram\Admin\Queries\SingleOrderGraphQLQuery;
use App\GraphQL\LoyaltyProgram\Admin\Queries\SingleSubscriptionGraphQLQuery;
use App\GraphQL\LoyaltyProgram\Admin\Queries\SingleTariffGraphQLQuery;
use App\GraphQL\LoyaltyProgram\Admin\Queries\SingleUserGraphQLQuery;
use App\GraphQL\LoyaltyProgram\Admin\Queries\SubscriptionsGraphQLQuery;
use App\GraphQL\LoyaltyProgram\Admin\Queries\TariffsGraphQLQuery;
use App\GraphQL\LoyaltyProgram\Admin\Queries\UsersGraphQLQuery;
use App\GraphQL\LoyaltyProgram\Admin\Types\AdminGraphQLType;
use App\GraphQL\LoyaltyProgram\Admin\Types\NotificationGraphQLType;
use App\GraphQL\LoyaltyProgram\Admin\Types\OrderGraphQLType;
use App\GraphQL\LoyaltyProgram\Admin\Types\OrderItemGraphQLType;
use App\GraphQL\LoyaltyProgram\Admin\Types\OrderPaymentGraphQLType;
use App\GraphQL\LoyaltyProgram\Admin\Types\OrderStatusHistoryGraphQLType;
use App\GraphQL\LoyaltyProgram\Admin\Types\ProductGraphQLType;
use App\GraphQL\LoyaltyProgram\Admin\Types\RoleGraphQLType;
use App\GraphQL\LoyaltyProgram\Admin\Types\SubscriptionGraphQLType;
use App\GraphQL\LoyaltyProgram\Admin\Types\TariffGraphQLType;
use App\GraphQL\LoyaltyProgram\Admin\Types\TariffInputGraphQLType;
use App\GraphQL\LoyaltyProgram\Client\Types\CardGraphQLType;
use App\GraphQL\LoyaltyProgram\Client\Types\QrGraphQLType;
use App\GraphQL\LoyaltyProgram\Client\Types\UserGraphQLType;
use App\GraphQL\Management\Types\AttachmentGraphQLType;
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
                OrdersGraphQLQuery::class,
                SingleTariffGraphQLQuery::class,
                SingleNotificationGraphQLQuery::class,
                SubscriptionsGraphQLQuery::class,
                SingleSubscriptionGraphQLQuery::class,
                SingleOrderGraphQLQuery::class,
            ],
            'types' => [
                UserGraphQLType::class,
                TariffGraphQLType::class,
                TariffInputGraphQLType::class,
                NotificationGraphQLType::class,
                SubscriptionGraphQLType::class,
                CardGraphQLType::class,
                QrGraphQLType::class,
                OrderGraphQLType::class,
                OrderItemGraphQLType::class,
                AdminGraphQLType::class,
                AttachmentGraphQLType::class,
                RoleGraphQLType::class,
                ProductGraphQLType::class,
                OrderStatusHistoryGraphQLType::class,
                OrderPaymentGraphQLType::class,
            ],
            'middleware' => ['auth:company-admin', 'is_soft_delete_token', 'loyalty_program', 'tenant_logging'],
        ];
    }
}
