<?php

namespace App\GraphQL\LoyaltyProgram\Admin;

use App\GraphQL\LoyaltyProgram\Admin\Mutations\CancelPaymentGraphQLMutation;
use App\GraphQL\LoyaltyProgram\Admin\Mutations\CreateNotificationGraphQLMutation;
use App\GraphQL\LoyaltyProgram\Admin\Mutations\CreateTariffGraphQLMutation;
use App\GraphQL\LoyaltyProgram\Admin\Mutations\CreateUserGraphQLMutation;
use App\GraphQL\LoyaltyProgram\Admin\Mutations\DeleteCardGraphQLMutation;
use App\GraphQL\LoyaltyProgram\Admin\Mutations\DeleteNotificationsGraphQLMutation;
use App\GraphQL\LoyaltyProgram\Admin\Mutations\DeleteTariffGraphQLMutation;
use App\GraphQL\LoyaltyProgram\Admin\Mutations\LoyaltyProgramImageGraphQLType;
use App\GraphQL\LoyaltyProgram\Admin\Mutations\SendNotificationGraphQLMutation;
use App\GraphQL\LoyaltyProgram\Admin\Mutations\UpdateCardGraphQLMutation;
use App\GraphQL\LoyaltyProgram\Admin\Mutations\UpdateNotificationGraphQLMutation;
use App\GraphQL\LoyaltyProgram\Admin\Mutations\UpdateShopGraphQLMutation;
use App\GraphQL\LoyaltyProgram\Admin\Mutations\UpdateSubscriptionGraphQLMutation;
use App\GraphQL\LoyaltyProgram\Admin\Mutations\UpdateTariffGraphQLMutation;
use App\GraphQL\LoyaltyProgram\Admin\Mutations\UpdateUserGraphQLMutation;
use App\GraphQL\LoyaltyProgram\Admin\Mutations\UploadLoyaltyProgramImageGraphQLMutation;
use App\GraphQL\LoyaltyProgram\Admin\Queries\CardsGraphQLQuery;
use App\GraphQL\LoyaltyProgram\Admin\Queries\NotificationsGraphQLQuery;
use App\GraphQL\LoyaltyProgram\Admin\Queries\OrdersGraphQLQuery;
use App\GraphQL\LoyaltyProgram\Admin\Queries\PaymentsGraphQLQuery;
use App\GraphQL\LoyaltyProgram\Admin\Queries\ProductsGraphQLQuery;
use App\GraphQL\LoyaltyProgram\Admin\Queries\ShopsGraphQLQuery;
use App\GraphQL\LoyaltyProgram\Admin\Queries\SingleCardGraphQLQuery;
use App\GraphQL\LoyaltyProgram\Admin\Queries\SingleNotificationGraphQLQuery;
use App\GraphQL\LoyaltyProgram\Admin\Queries\SingleOrderGraphQLQuery;
use App\GraphQL\LoyaltyProgram\Admin\Queries\SinglePaymentGraphQLQuery;
use App\GraphQL\LoyaltyProgram\Admin\Queries\SingleProductGraphQLQuery;
use App\GraphQL\LoyaltyProgram\Admin\Queries\SingleShopGraphQLQuery;
use App\GraphQL\LoyaltyProgram\Admin\Queries\SingleSubscriptionGraphQLQuery;
use App\GraphQL\LoyaltyProgram\Admin\Queries\SingleTariffGraphQLQuery;
use App\GraphQL\LoyaltyProgram\Admin\Queries\SingleUserGraphQLQuery;
use App\GraphQL\LoyaltyProgram\Admin\Queries\SubscriptionsGraphQLQuery;
use App\GraphQL\LoyaltyProgram\Admin\Queries\TariffsGraphQLQuery;
use App\GraphQL\LoyaltyProgram\Admin\Queries\UsersGraphQLQuery;
use App\GraphQL\LoyaltyProgram\Admin\Types\CardGraphQLType;
use App\GraphQL\LoyaltyProgram\Admin\Types\CardInputGraphQLType;
use App\GraphQL\LoyaltyProgram\Admin\Types\NotificationGraphQLType;
use App\GraphQL\LoyaltyProgram\Admin\Types\NotificationInputGraphQLType;
use App\GraphQL\LoyaltyProgram\Admin\Types\OrderGraphQLType;
use App\GraphQL\LoyaltyProgram\Admin\Types\OrderItemGraphQLType;
use App\GraphQL\LoyaltyProgram\Admin\Types\OrderPaymentGraphQLType;
use App\GraphQL\LoyaltyProgram\Admin\Types\OrderStatusHistoryGraphQLType;
use App\GraphQL\LoyaltyProgram\Admin\Types\PaymentGraphQLType;
use App\GraphQL\LoyaltyProgram\Admin\Types\ProductGraphQLType;
use App\GraphQL\LoyaltyProgram\Admin\Types\ShopGraphQLType;
use App\GraphQL\LoyaltyProgram\Admin\Types\ShopInputGraphQLType;
use App\GraphQL\LoyaltyProgram\Admin\Types\SubscriptionGraphQLType;
use App\GraphQL\LoyaltyProgram\Admin\Types\SubscriptionInputGraphQLType;
use App\GraphQL\LoyaltyProgram\Admin\Types\TariffGraphQLType;
use App\GraphQL\LoyaltyProgram\Admin\Types\TariffInputGraphQLType;
use App\GraphQL\LoyaltyProgram\Admin\Types\UserGraphQLType;
use App\GraphQL\LoyaltyProgram\Admin\Types\UserInputGraphQLType;
use App\GraphQL\LoyaltyProgram\Client\Types\QrGraphQLType;
use App\GraphQL\Management\Types\AdminGraphQLType;
use App\GraphQL\Management\Types\AttachmentGraphQLType;
use App\GraphQL\Management\Types\PermissionBranchItemGraphQLType;
use App\GraphQL\Management\Types\PermissionEditableItemGraphQLType;
use App\GraphQL\Management\Types\PermissionGraphQLType;
use App\GraphQL\Management\Types\PermissionGroupGraphQLType;
use App\GraphQL\Management\Types\RoleGraphQLType;
use App\GraphQL\Management\Types\TemplateThemeGraphQLType;
use Rebing\GraphQL\Support\Contracts\ConfigConvertible;

class LoyaltyProgramAdminGraphQLSchema implements ConfigConvertible
{
    public function toConfig(): array
    {
        return [
            'mutation' => [
                CreateTariffGraphQLMutation::class,
                CreateNotificationGraphQLMutation::class,
                CreateUserGraphQLMutation::class,
                UpdateTariffGraphQLMutation::class,
                UpdateNotificationGraphQLMutation::class,
                UpdateShopGraphQLMutation::class,
                UpdateUserGraphQLMutation::class,
                UpdateCardGraphQLMutation::class,
                UpdateSubscriptionGraphQLMutation::class,
                DeleteTariffGraphQLMutation::class,
                DeleteNotificationsGraphQLMutation::class,
                DeleteCardGraphQLMutation::class,
                SendNotificationGraphQLMutation::class,
                UploadLoyaltyProgramImageGraphQLMutation::class,
                CancelPaymentGraphQLMutation::class,
            ],
            'query' => [
                UsersGraphQLQuery::class,
                TariffsGraphQLQuery::class,
                NotificationsGraphQLQuery::class,
                OrdersGraphQLQuery::class,
                SubscriptionsGraphQLQuery::class,
                ShopsGraphQLQuery::class,
                CardsGraphQLQuery::class,
                PaymentsGraphQLQuery::class,
                ProductsGraphQLQuery::class,
                SingleUserGraphQLQuery::class,
                SingleTariffGraphQLQuery::class,
                SingleNotificationGraphQLQuery::class,
                SingleSubscriptionGraphQLQuery::class,
                SingleOrderGraphQLQuery::class,
                SingleShopGraphQLQuery::class,
                SingleCardGraphQLQuery::class,
                SinglePaymentGraphQLQuery::class,
                SingleProductGraphQLQuery::class,
            ],
            'types' => [
                UserGraphQLType::class,
                TariffGraphQLType::class,
                NotificationGraphQLType::class,
                SubscriptionGraphQLType::class,
                CardGraphQLType::class,
                QrGraphQLType::class,
                OrderGraphQLType::class,
                OrderItemGraphQLType::class,
                AdminGraphQLType::class,
                ProductGraphQLType::class,
                OrderStatusHistoryGraphQLType::class,
                OrderPaymentGraphQLType::class,
                ShopGraphQLType::class,
                AttachmentGraphQLType::class,
                PaymentGraphQLType::class,
                LoyaltyProgramImageGraphQLType::class,
                TariffInputGraphQLType::class,
                CardInputGraphQLType::class,
                SubscriptionInputGraphQLType::class,
                UserInputGraphQLType::class,
                ShopInputGraphQLType::class,
                NotificationInputGraphQLType::class,
                TemplateThemeGraphQLType::class,
                RoleGraphQLType::class,
                PermissionGroupGraphQLType::class,
                PermissionEditableItemGraphQLType::class,
                PermissionBranchItemGraphQLType::class,
                PermissionGraphQLType::class,
            ],
            'middleware' => ['auth:company-admin', 'is_soft_delete_token', 'loyalty_program', 'tenant_logging'],
        ];
    }
}
