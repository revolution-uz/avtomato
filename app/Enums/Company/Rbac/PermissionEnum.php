<?php

namespace App\Enums\Company\Rbac;

use App\Enums\FeatureEnum;

/**
 * To add new permission:
 * 1. Add new permission as a case to PermissionEnum
 * 2. Add new permission to list() method and set group
 * 3. Add new permission to getIsBranchRelated() method and set branch related or not
 * 4. Add new permission to getFeatureId() method and set feature id
 */
enum PermissionEnum: string
{
    // Profile management
    case PROFILE_EDIT = 'profile_edit';
    case PROFILE_CHANGE_PASSWORD = 'profile_change_password';
    case PROFILE_CHANGE_LANGUAGE = 'profile_change_language';
    case PROFILE_CHANGE_AVATAR = 'profile_change_avatar';
    case PROFILE_DEVICES = 'profile_devices';
    case PROFILE_REVOKE_DEVICE = 'profile_revoke_device';

    // Manage tariffs
    case BALANCE_FUND_VIEW_ALL = 'balance_fund_view_all';
    case BALANCE_FUND_VIEW = 'balance_fund_view';
    case BALANCE_FUND_DELETE = 'balance_fund_delete';
    case BALANCE_FUND_CREATE = 'balance_fund_create';
    case BALANCE_FUND_CONFIRM = 'balance_fund_confirm';

    // Balance charges
    case BALANCE_CHARGE_VIEW_ALL = 'balance_charge_view_all';
    case BALANCE_CHARGE_VIEW = 'balance_charge_view';
    case BALANCE_CHARGE_DELETE = 'balance_charge_delete';
    case BALANCE_CHARGE_CREATE = 'balance_charge_create';
    case BALANCE_CHARGE_CONFIRM = 'balance_charge_confirm';

    // Manage Admins
    case ADMIN_VIEW_ALL = 'admin_view_all';
    case ADMIN_VIEW = 'admin_view';
    case ADMIN_CREATE = 'admin_create';
    case ADMIN_EDIT = 'admin_edit';
    case ADMIN_DELETE = 'admin_delete';
    case ADMIN_CHANGE_PASSWORD = 'admin_change_password';
    case ADMIN_CHANGE_AVATAR = 'admin_change_avatar';
    case ADMIN_CHANGE_ROLE = 'admin_change_role';
    case ADMIN_DEVICES = 'admin_devices';
    case ADMIN_REVOKE_DEVICE = 'admin_revoke_device';

    // Manage Themes
    case PROFILE_CHANGE_THEME = 'profile_change_theme';
    case ADMIN_CHANGE_THEME = 'admin_change_theme';

    // MANAGE ROLES
    case ROLE_VIEW_ALL = 'role_view_all';
    case ROLE_VIEW = 'role_view';
    case ROLE_CREATE = 'role_create';
    case ROLE_EDIT = 'role_edit';
    case ROLE_DELETE = 'role_delete';
    case ROLE_PERMISSIONS = 'role_permissions';

    // Manage Customers
    case CUSTOMER_INFO_BY_PASSPORT = 'customer_info_by_passport';
    case CUSTOMER_CREATE = 'customer_create';
    case CUSTOMER_EDIT = 'customer_edit';
    case CUSTOMER_VIEW_ALL = 'customer_view_all';
    case CUSTOMER_VIEW = 'customer_view';
    case CUSTOMER_VIEW_INSTALLMENTS = 'customer_view_installments';
    case CUSTOMER_CARD_VIEW = 'customer_card_view';
    case CUSTOMER_CARD_EDIT = 'customer_card_edit';
    case CUSTOMER_CARD_REMOVE = 'customer_card_remove';
    case CUSTOMER_TAG_ATTACH = 'customer_tag_attach';
    case CUSTOMER_TAG_DETACH = 'customer_tag_detach';
    case CUSTOMER_TAG_VIEW = 'customer_tag_view';
    case TAG_CREATE = 'tag_create';
    case TAG_EDIT = 'tag_edit';
    case TAG_DELETE = 'tag_delete';
    case TAG_VIEW = 'tag_view';
    case TAG_VIEW_ALL = 'tag_view_all';

    // Calculator installment
    case CALCULATE_INSTALLMENT = 'calculate_installment';
    case CALCULATOR_EXPORT = 'calculator_export';

    // Manage installment
    case INSTALLMENT_VIEW_ALL = 'installment_view_all';
    case INSTALLMENT_VIEW = 'installment_view';
    case INSTALLMENT_VIEW_STATISTICS = 'installment_view_statistics';
    case INSTALLMENT_START = 'installment_start';
    case INSTALLMENT_CANCEL = 'installment_cancel';
    case INSTALLMENT_FREEZE = 'installment_freeze';
    case INSTALLMENT_FINISH = 'installment_finish';
    case INSTALLMENT_CUSTOMER_LIST = 'installment_customer_list';
    case INSTALLMENT_CREATE = 'installment_create';
    case INSTALLMENT_EDIT = 'installment_edit';
    case INSTALLMENT_DELETE = 'installment_delete';
    case INSTALLMENT_ATTACH_CARD = 'installment_attach_card';
    case INSTALLMENT_DETACH_CARD = 'installment_detach_card';
    case INSTALLMENT_ATTACH_GUARANTOR = 'installment_attach_guarantor';
    case INSTALLMENT_UPDATE_GUARANTOR = 'installment_update_guarantor';
    case INSTALLMENT_DETACH_GUARANTOR = 'installment_detach_guarantor';
    case INSTALLMENT_CREATE_COMMENT = 'installment_create_comment';
    case INSTALLMENT_CREATE_TRANSACTION = 'installment_create_transaction';
    case INSTALLMENT_UPDATE_COMMENT = 'installment_update_comment';
    case INSTALLMENT_DELETE_COMMENT = 'installment_delete_comment';

    case INSTALLMENT_VIEW_DOCUMENT = 'installment_view_document';
    case INSTALLMENT_UPLOAD_DOCUMENT = 'installment_upload_document';
    case INSTALLMENT_DELETE_DOCUMENT = 'installment_delete_document';

    case DASHBOARD_INFO_VIEW = 'dashboard_info_view';

    // Manage tariffs
    case TARIFF_VIEW_ALL = 'tariff_view_all';
    case TARIFF_VIEW = 'tariff_view';
    case TARIFF_EDIT = 'tariff_edit';
    case TARIFF_DELETE = 'tariff_delete';
    case TARIFF_CREATE = 'tariff_create';

    // Manage transactions
    case TRANSACTION_VIEW_ALL = 'transaction_view_all';
    case TRANSACTION_ATTEMPT_VIEW_ALL = 'transaction_attempt_view_all';
    case TRANSACTION_VIEW = 'transaction_view';
    case TRANSACTION_CREATE = 'transaction_create';
    case TRANSACTION_EDIT = 'transaction_edit';
    case TRANSACTION_DELETE = 'transaction_delete';
    case TRANSACTION_REVERSE = 'transaction_cancel';

    // Manage transaction types
    case TRANSACTION_TYPE_VIEW_ALL = 'transaction_type_view_all';
    case TRANSACTION_TYPE_VIEW = 'transaction_type_view';
    case TRANSACTION_TYPE_CREATE = 'transaction_type_create';
    case TRANSACTION_TYPE_EDIT = 'transaction_type_edit';
    case TRANSACTION_TYPE_DELETE = 'transaction_type_delete';

    // Manage branches
    case BRANCH_VIEW_ALL = 'branch_view_all';
    case BRANCH_VIEW = 'branch_view';
    case BRANCH_EDIT = 'branch_edit';
    case BRANCH_DELETE = 'branch_delete';
    case BRANCH_CREATE = 'branch_create';
    case STATISTICS_BY_BRANCHES = 'statistics_by_branches';

    // Scoring credit history
    case SCORING_CREDIT_HISTORY_VIEW_ALL = 'scoring_credit_history_view_all';
    case SCORING_CREDIT_HISTORY_VIEW = 'scoring_credit_history_view';
    case SCORING_CREDIT_HISTORY_CREATE = 'scoring_credit_history_create';
    case SCORING_CREDIT_HISTORY_DELETE = 'scoring_credit_history_delete';

    // Scoring inps history
    case SCORING_INPS_HISTORY_VIEW_ALL = 'scoring_inps_history_view_all';
    case SCORING_INPS_HISTORY_VIEW = 'scoring_inps_history_view';
    case SCORING_INPS_HISTORY_CREATE = 'scoring_inps_history_create';
    case SCORING_INPS_HISTORY_DELETE = 'scoring_inps_history_delete';

    // Scoring pension info
    case SCORING_PENSION_INFO_VIEW_ALL = 'scoring_pension_info_view_all';
    case SCORING_PENSION_INFO_VIEW = 'scoring_pension_info_view';
    case SCORING_PENSION_INFO_CREATE = 'scoring_pension_info_create';
    case SCORING_PENSION_INFO_DELETE = 'scoring_pension_info_delete';

    // Scoring MIB
    case SCORING_MIB_INFO_VIEW_ALL = 'scoring_mib_info_view_all';
    case SCORING_MIB_INFO_VIEW = 'scoring_mib_info_view';
    case SCORING_MIB_INFO_CREATE = 'scoring_mib_info_create';
    case SCORING_MIB_INFO_DELETE = 'scoring_mib_info_delete';

    // Scoring TAX
    case SCORING_TAX_INFO_VIEW_ALL = 'scoring_tax_info_view_all';
    case SCORING_TAX_INFO_VIEW = 'scoring_tax_info_view';
    case SCORING_TAX_INFO_CREATE = 'scoring_tax_info_create';
    case SCORING_TAX_INFO_DELETE = 'scoring_tax_info_delete';

    // Scoring Humo
    case SCORING_HUMO_VIEW_ALL = 'scoring_humo_view_all';
    case SCORING_HUMO_VIEW = 'scoring_humo_view';
    case SCORING_HUMO_CREATE = 'scoring_humo_create';
    case SCORING_HUMO_DELETE = 'scoring_humo_delete';

    // Scoring Uzcard
    case SCORING_UZCARD_VIEW_ALL = 'scoring_uzcard_view_all';
    case SCORING_UZCARD_VIEW = 'scoring_uzcard_view';
    case SCORING_UZCARD_CREATE = 'scoring_uzcard_create';
    case SCORING_UZCARD_DELETE = 'scoring_uzcard_delete';

    // Credit History in Partners
    case SCORING_ROYXAT_CREDIT_VIEW_ALL = 'scoring_royxat_credit_view_all';
    case SCORING_UZUM_NASIYA_LIMIT_VIEW_ALL = 'scoring_uzum_nasiya_limit_view_all';

    // Activity log
    case ACTIVITY_LOG_VIEW = 'activity_log_view';
    case ACTIVITY_LOG_VIEW_ALL = 'activity_log_view_all';
    case ACTIVITY_LOG_VIEW_OWN = 'activity_log_view_own';

    // Company Settings
    case SETTINGS_EDIT = 'settings_edit';
    // Ecommerce
    case PRODUCT_CATEGORY_CREATE = 'e_commerce_product_category_create';
    case PRODUCT_CATEGORY_VIEW = 'e_commerce_product_category_view';
    case PRODUCT_CATEGORY_VIEW_ALL = 'e_commerce_product_category_view_all';
    case PRODUCT_CATEGORY_EDIT = 'e_commerce_product_category_edit';
    case PRODUCT_CATEGORY_DELETE = 'e_commerce_product_category_delete';
    case BANNER_CREATE = 'e_commerce_banner_create';
    case BANNER_EDIT = 'e_commerce_banner_edit';
    case BANNER_DELETE = 'e_commerce_banner_delete';
    case BANNER_VIEW = 'e_commerce_banner_view';
    case BANNER_VIEW_ALL = 'e_commerce_banner_view_all';
    case PRODUCT_BRAND_CREATE = 'e_commerce_product_brand_create';
    case PRODUCT_BRAND_EDIT = 'e_commerce_product_brand_edit';
    case PRODUCT_BRAND_DELETE = 'e_commerce_product_brand_delete';
    case PRODUCT_BRAND_VIEW = 'e_commerce_product_brand_view';
    case PRODUCT_BRAND_VIEW_ALL = 'e_commerce_product_brand_view_all';
    case PRODUCT_OPTION_VALUE_CREATE = 'e_commerce_product_option_value_create';
    case PRODUCT_OPTION_VALUE_VIEW = 'e_commerce_product_option_value_view';
    case PRODUCT_OPTION_VALUE_EDIT = 'e_commerce_product_option_value_edit';
    case PRODUCT_OPTION_VALUE_DELETE = 'e_commerce_product_option_value_delete';
    case PRODUCT_OPTION_VALUE_VIEW_ALL = 'e_commerce_product_option_value_view_all';
    case PRODUCT_OPTION_GROUP_CREATE = 'e_commerce_product_option_group_create';
    case PRODUCT_OPTION_GROUP_EDIT = 'e_commerce_product_option_group_edit';
    case PRODUCT_OPTION_GROUP_DELETE = 'e_commerce_product_option_group_delete';
    case PRODUCT_OPTION_GROUP_VIEW = 'e_commerce_product_option_group_view';
    case PRODUCT_OPTION_GROUP_VIEW_ALL = 'e_commerce_product_option_group_view_all';
    case PRODUCT_OPTION_CREATE = 'e_commerce_product_option_create';
    case PRODUCT_OPTION_EDIT = 'e_commerce_product_option_edit';
    case PRODUCT_OPTION_DELETE = 'e_commerce_product_option_delete';
    case PRODUCT_OPTION_VIEW = 'e_commerce_product_option_view';
    case PRODUCT_OPTION_VIEW_ALL = 'e_commerce_product_option_view_all';
    case PRODUCT_JOIN_OPTION_CREATE = 'e_commerce_product_join_option_create';
    case PRODUCT_JOIN_OPTION_EDIT = 'e_commerce_product_join_option_edit';
    case PRODUCT_JOIN_OPTION_DELETE = 'e_commerce_product_join_option_delete';
    case PRODUCT_JOIN_OPTION_VIEW = 'e_commerce_product_join_option_view';
    case PRODUCT_JOIN_OPTION_VIEW_ALL = 'e_commerce_product_join_option_view_all';
    case PRODUCT_VARIATION_CREATE = 'e_commerce_product_variation_create';
    case PRODUCT_VARIATION_EDIT = 'e_commerce_product_variation_edit';
    case PRODUCT_VARIATION_DELETE = 'e_commerce_product_variation_delete';
    case PRODUCT_VARIATION_VIEW = 'e_commerce_product_variation_view';
    case PRODUCT_VARIATION_VIEW_ALL = 'e_commerce_product_variation_view_all';
    case PRODUCT_CREATE = 'e_commerce_product_create';
    case PRODUCT_EDIT = 'e_commerce_product_edit';
    case PRODUCT_DELETE = 'e_commerce_product_delete';
    case PRODUCT_VIEW = 'e_commerce_product_view';
    case PRODUCT_VIEW_ALL = 'e_commerce_product_view_all';
    case ORDERS_VIEW_ALL = 'e_commerce_orders_view_all';
    case ORDERS_VIEW = 'e_commerce_orders_view';
    case ORDER_STATUS_CREATE = 'e_commerce_order_statuses_create';
    case ORDER_STATUS_EDIT = 'e_commerce_order_statuses_edit';
    case ORDER_STATUS_DELETE = 'e_commerce_order_statuses_delete';
    case ORDER_STATUS_VIEW = 'e_commerce_order_statuses_view';
    case ORDER_STATUS_VIEW_ALL = 'e_commerce_order_statuses_view_all';
    case ORDER_ITEMS_VIEW_ALL = 'e_commerce_order_items_view_all';

    case USERS_VIEW_ALL = 'e_commerce_users_view_all';

    case USERS_VIEW = 'e_commerce_users_view';

    public static function getIsBranchRelated(PermissionEnum $permission): bool
    {
        return match ($permission) {
            // not branch related
            self::PROFILE_EDIT,
            self::PROFILE_CHANGE_PASSWORD,
            self::PROFILE_CHANGE_THEME,
            self::PROFILE_CHANGE_LANGUAGE,
            self::PROFILE_CHANGE_AVATAR,
            self::PROFILE_DEVICES,
            self::PROFILE_REVOKE_DEVICE,

            self::BALANCE_FUND_VIEW_ALL,
            self::BALANCE_FUND_VIEW,
            self::BALANCE_FUND_CONFIRM,
            self::BALANCE_FUND_DELETE,
            self::BALANCE_FUND_CREATE,

            self::BALANCE_CHARGE_VIEW_ALL,
            self::BALANCE_CHARGE_VIEW,
            self::BALANCE_CHARGE_CONFIRM,
            self::BALANCE_CHARGE_DELETE,
            self::BALANCE_CHARGE_CREATE,

            self::ADMIN_VIEW_ALL,
            self::ADMIN_VIEW,
            self::ADMIN_CREATE,
            self::ADMIN_EDIT,
            self::ADMIN_DELETE,
            self::ADMIN_CHANGE_PASSWORD,
            self::ADMIN_CHANGE_AVATAR,
            self::ADMIN_CHANGE_ROLE,
            self::ADMIN_DEVICES,
            self::ADMIN_REVOKE_DEVICE,
            self::ADMIN_CHANGE_THEME,

            self::CUSTOMER_VIEW_ALL,
            self::CUSTOMER_VIEW,
            self::CUSTOMER_VIEW_INSTALLMENTS,
            self::CUSTOMER_CREATE,
            self::CUSTOMER_INFO_BY_PASSPORT,
            self::CUSTOMER_EDIT,
            self::CUSTOMER_CARD_VIEW,
            self::CUSTOMER_CARD_EDIT,
            self::CUSTOMER_CARD_REMOVE,
            self::CUSTOMER_TAG_VIEW,
            self::CUSTOMER_TAG_ATTACH,
            self::CUSTOMER_TAG_DETACH,
            self::TAG_VIEW_ALL,
            self::TAG_VIEW,
            self::TAG_CREATE,
            self::TAG_EDIT,
            self::TAG_DELETE,

            self::ROLE_VIEW_ALL,
            self::ROLE_VIEW,
            self::ROLE_CREATE,
            self::ROLE_EDIT,
            self::ROLE_DELETE,
            self::ROLE_PERMISSIONS,

            self::CALCULATE_INSTALLMENT,
            self::CALCULATOR_EXPORT,

            self::TARIFF_VIEW_ALL,
            self::TARIFF_VIEW,
            self::TARIFF_EDIT,
            self::TARIFF_DELETE,
            self::TARIFF_CREATE,

            self::TRANSACTION_TYPE_VIEW_ALL,
            self::TRANSACTION_TYPE_VIEW,
            self::TRANSACTION_TYPE_CREATE,
            self::TRANSACTION_TYPE_EDIT,
            self::TRANSACTION_TYPE_DELETE,

            self::BRANCH_VIEW_ALL,
            self::BRANCH_VIEW,
            self::BRANCH_EDIT,
            self::BRANCH_DELETE,
            self::BRANCH_CREATE,
            self::STATISTICS_BY_BRANCHES,

            self::SCORING_CREDIT_HISTORY_VIEW_ALL,
            self::SCORING_CREDIT_HISTORY_VIEW,
            self::SCORING_CREDIT_HISTORY_CREATE,
            self::SCORING_CREDIT_HISTORY_DELETE,

            self::SCORING_INPS_HISTORY_VIEW_ALL,
            self::SCORING_INPS_HISTORY_VIEW,
            self::SCORING_INPS_HISTORY_CREATE,
            self::SCORING_INPS_HISTORY_DELETE,

            self::SCORING_PENSION_INFO_VIEW_ALL,
            self::SCORING_PENSION_INFO_VIEW,
            self::SCORING_PENSION_INFO_CREATE,
            self::SCORING_PENSION_INFO_DELETE,

            self::SCORING_MIB_INFO_VIEW_ALL,
            self::SCORING_MIB_INFO_VIEW,
            self::SCORING_MIB_INFO_CREATE,
            self::SCORING_MIB_INFO_DELETE,

            self::SCORING_TAX_INFO_VIEW_ALL,
            self::SCORING_TAX_INFO_VIEW,
            self::SCORING_TAX_INFO_CREATE,
            self::SCORING_TAX_INFO_DELETE,

            self::SCORING_HUMO_VIEW_ALL,
            self::SCORING_HUMO_VIEW,
            self::SCORING_HUMO_CREATE,
            self::SCORING_HUMO_DELETE,

            self::SCORING_UZCARD_VIEW_ALL,
            self::SCORING_UZCARD_VIEW,
            self::SCORING_UZCARD_CREATE,
            self::SCORING_UZCARD_DELETE,

            self::SCORING_ROYXAT_CREDIT_VIEW_ALL,

            self::ACTIVITY_LOG_VIEW,
            self::ACTIVITY_LOG_VIEW_ALL,
            self::ACTIVITY_LOG_VIEW_OWN,

            self::DASHBOARD_INFO_VIEW,

            self::SETTINGS_EDIT,
            //e-commerce
            self::PRODUCT_CATEGORY_CREATE,
            self::PRODUCT_CATEGORY_EDIT,
            self::PRODUCT_CATEGORY_DELETE,
            self::PRODUCT_CATEGORY_VIEW,
            self::PRODUCT_CATEGORY_VIEW_ALL,
            self::BANNER_CREATE,
            self::BANNER_EDIT,
            self::BANNER_DELETE,
            self::BANNER_VIEW_ALL,
            self::BANNER_VIEW,
            self::PRODUCT_BRAND_CREATE,
            self::PRODUCT_BRAND_EDIT,
            self::PRODUCT_BRAND_DELETE,
            self::PRODUCT_BRAND_VIEW,
            self::PRODUCT_BRAND_VIEW_ALL,
            self::PRODUCT_OPTION_VALUE_CREATE,
            self::PRODUCT_OPTION_VALUE_EDIT,
            self::PRODUCT_OPTION_VALUE_DELETE,
            self::PRODUCT_OPTION_VALUE_VIEW,
            self::PRODUCT_OPTION_VALUE_VIEW_ALL,
            self::PRODUCT_OPTION_GROUP_CREATE,
            self::PRODUCT_OPTION_GROUP_EDIT,
            self::PRODUCT_OPTION_GROUP_DELETE,
            self::PRODUCT_OPTION_GROUP_VIEW,
            self::PRODUCT_OPTION_GROUP_VIEW_ALL,
            self::PRODUCT_OPTION_CREATE,
            self::PRODUCT_OPTION_EDIT,
            self::PRODUCT_OPTION_VIEW_ALL,
            self::PRODUCT_OPTION_VIEW,
            self::PRODUCT_OPTION_DELETE,
            self::PRODUCT_VARIATION_CREATE,
            self::PRODUCT_VARIATION_EDIT,
            self::PRODUCT_VARIATION_DELETE,
            self::PRODUCT_VARIATION_VIEW,
            self::PRODUCT_VARIATION_VIEW_ALL,
            self::PRODUCT_JOIN_OPTION_CREATE,
            self::PRODUCT_JOIN_OPTION_EDIT,
            self::PRODUCT_JOIN_OPTION_DELETE,
            self::PRODUCT_JOIN_OPTION_VIEW,
            self::PRODUCT_JOIN_OPTION_VIEW_ALL,
            self::PRODUCT_CREATE,
            self::PRODUCT_EDIT,
            self::PRODUCT_DELETE,
            self::PRODUCT_VIEW,
            self::PRODUCT_VIEW_ALL,
            self::ORDER_STATUS_CREATE,
            self::ORDER_STATUS_EDIT,
            self::ORDER_STATUS_DELETE,
            self::ORDER_STATUS_VIEW,
            self::ORDER_STATUS_VIEW_ALL,
            self::ORDERS_VIEW,
            self::ORDERS_VIEW_ALL,
            self::ORDER_ITEMS_VIEW_ALL,
            self::USERS_VIEW_ALL,
            self::USERS_VIEW,

            // branch related
            self::INSTALLMENT_VIEW_ALL,
            self::INSTALLMENT_VIEW,
            self::INSTALLMENT_VIEW_STATISTICS,
            self::INSTALLMENT_CREATE,
            self::INSTALLMENT_START,
            self::INSTALLMENT_CANCEL,
            self::INSTALLMENT_FREEZE,
            self::INSTALLMENT_FINISH,
            self::INSTALLMENT_EDIT,
            self::INSTALLMENT_DELETE,
            self::INSTALLMENT_ATTACH_CARD,
            self::INSTALLMENT_DETACH_CARD,
            self::INSTALLMENT_CUSTOMER_LIST,
            self::INSTALLMENT_ATTACH_GUARANTOR,
            self::INSTALLMENT_UPDATE_GUARANTOR,
            self::INSTALLMENT_DETACH_GUARANTOR,

            self::INSTALLMENT_CREATE_COMMENT,
            self::INSTALLMENT_UPDATE_COMMENT,
            self::INSTALLMENT_DELETE_COMMENT,

            self::INSTALLMENT_VIEW_DOCUMENT,
            self::INSTALLMENT_UPLOAD_DOCUMENT,
            self::INSTALLMENT_DELETE_DOCUMENT,

            self::INSTALLMENT_CREATE_TRANSACTION,

            self::TRANSACTION_ATTEMPT_VIEW_ALL,
            self::TRANSACTION_VIEW_ALL,
            self::TRANSACTION_VIEW,
            self::TRANSACTION_CREATE,
            self::TRANSACTION_EDIT,
            self::TRANSACTION_DELETE,
            self::TRANSACTION_REVERSE => true,
        };
    }

    public static function list(): array
    {
        return [
            PermissionGroupEnum::ADMIN_MANAGEMENT->value => [
                self::ADMIN_VIEW_ALL->value,
                self::ADMIN_VIEW->value,
                self::ADMIN_CREATE->value,
                self::ADMIN_EDIT->value,
                self::ADMIN_DELETE->value,
                self::ADMIN_CHANGE_PASSWORD->value,
                self::ADMIN_CHANGE_AVATAR->value,
                self::ADMIN_CHANGE_ROLE->value,
                self::ADMIN_DEVICES->value,
                self::ADMIN_REVOKE_DEVICE->value,
                self::ADMIN_CHANGE_THEME->value,
            ],
            PermissionGroupEnum::CUSTOMER_MANAGEMENT->value => [
                self::CUSTOMER_VIEW_ALL->value,
                self::CUSTOMER_VIEW->value,
                self::CUSTOMER_VIEW_INSTALLMENTS->value,
                self::CUSTOMER_CREATE->value,
                self::CUSTOMER_INFO_BY_PASSPORT->value,
                self::CUSTOMER_EDIT->value,
                self::CUSTOMER_CARD_VIEW->value,
                self::CUSTOMER_CARD_EDIT->value,
                self::CUSTOMER_CARD_REMOVE->value,
                self::CUSTOMER_TAG_VIEW->value,
                self::CUSTOMER_TAG_ATTACH->value,
                self::CUSTOMER_TAG_DETACH->value,
                self::TAG_CREATE->value,
                self::TAG_EDIT->value,
                self::TAG_DELETE->value,
                self::TAG_VIEW_ALL->value,
                self::TAG_VIEW->value,
            ],
            PermissionGroupEnum::ROLE_MANAGEMENT->value => [
                self::ROLE_VIEW_ALL->value,
                self::ROLE_VIEW->value,
                self::ROLE_CREATE->value,
                self::ROLE_EDIT->value,
                self::ROLE_DELETE->value,
                self::ROLE_PERMISSIONS->value,
            ],
            PermissionGroupEnum::CALCULATOR_INSTALLMENT->value => [
                self::CALCULATE_INSTALLMENT->value,
                self::CALCULATOR_EXPORT->value,
            ],
            PermissionGroupEnum::INSTALLMENT_MANAGEMENT->value => [
                self::INSTALLMENT_VIEW_ALL->value,
                self::INSTALLMENT_VIEW->value,
                self::INSTALLMENT_VIEW_STATISTICS->value,
                self::INSTALLMENT_CREATE->value,
                self::INSTALLMENT_START->value,
                self::INSTALLMENT_CANCEL->value,
                self::INSTALLMENT_FREEZE->value,
                self::INSTALLMENT_FINISH->value,
                self::INSTALLMENT_CUSTOMER_LIST->value,
                self::INSTALLMENT_EDIT->value,
                self::INSTALLMENT_DELETE->value,
                self::INSTALLMENT_ATTACH_CARD->value,
                self::INSTALLMENT_DETACH_CARD->value,
                self::INSTALLMENT_ATTACH_GUARANTOR->value,
                self::INSTALLMENT_UPDATE_GUARANTOR->value,
                self::INSTALLMENT_DETACH_GUARANTOR->value,
                self::INSTALLMENT_CREATE_COMMENT->value,
                self::INSTALLMENT_UPDATE_COMMENT->value,
                self::INSTALLMENT_DELETE_COMMENT->value,
                self::INSTALLMENT_VIEW_DOCUMENT->value,
                self::INSTALLMENT_UPLOAD_DOCUMENT->value,
                self::INSTALLMENT_DELETE_DOCUMENT->value,
                self::INSTALLMENT_CREATE_TRANSACTION->value,
                self::DASHBOARD_INFO_VIEW->value,
            ],
            PermissionGroupEnum::TARIFF_MANAGEMENT->value => [
                self::TARIFF_VIEW_ALL->value,
                self::TARIFF_VIEW->value,
                self::TARIFF_EDIT->value,
                self::TARIFF_DELETE->value,
                self::TARIFF_CREATE->value,
            ],
            PermissionGroupEnum::TRANSACTION_MANAGEMENT->value => [
                self::TRANSACTION_ATTEMPT_VIEW_ALL->value,
                self::TRANSACTION_VIEW_ALL->value,
                self::TRANSACTION_VIEW->value,
                self::TRANSACTION_CREATE->value,
                self::TRANSACTION_EDIT->value,
                self::TRANSACTION_DELETE->value,
                self::TRANSACTION_REVERSE->value,
            ],
            PermissionGroupEnum::TRANSACTION_TYPE_MANAGEMENT->value => [
                self::TRANSACTION_TYPE_VIEW_ALL->value,
                self::TRANSACTION_TYPE_VIEW->value,
                self::TRANSACTION_TYPE_CREATE->value,
                self::TRANSACTION_TYPE_EDIT->value,
                self::TRANSACTION_TYPE_DELETE->value,
            ],
            PermissionGroupEnum::BRANCH_MANAGEMENT->value => [
                self::BRANCH_VIEW_ALL->value,
                self::BRANCH_VIEW->value,
                self::BRANCH_EDIT->value,
                self::BRANCH_DELETE->value,
                self::BRANCH_CREATE->value,
                self::STATISTICS_BY_BRANCHES,
            ],
            PermissionGroupEnum::COMPANY_MANAGEMENT->value => [
                self::SETTINGS_EDIT->value,
            ],
            PermissionGroupEnum::SCORING_CREDIT_HISTORY->value => [
                self::SCORING_CREDIT_HISTORY_VIEW_ALL->value,
                self::SCORING_CREDIT_HISTORY_VIEW->value,
                self::SCORING_CREDIT_HISTORY_CREATE->value,
                self::SCORING_CREDIT_HISTORY_DELETE->value,
            ],
            PermissionGroupEnum::SCORING_INPS_HISTORY->value => [
                self::SCORING_INPS_HISTORY_VIEW_ALL->value,
                self::SCORING_INPS_HISTORY_VIEW->value,
                self::SCORING_INPS_HISTORY_CREATE->value,
                self::SCORING_INPS_HISTORY_DELETE->value,
            ],
            PermissionGroupEnum::SCORING_PENSION_INFO->value => [
                self::SCORING_PENSION_INFO_VIEW_ALL->value,
                self::SCORING_PENSION_INFO_VIEW->value,
                self::SCORING_PENSION_INFO_CREATE->value,
                self::SCORING_PENSION_INFO_DELETE->value,
            ],
            PermissionGroupEnum::SCORING_MIB_INFO->value => [
                self::SCORING_MIB_INFO_VIEW_ALL->value,
                self::SCORING_MIB_INFO_VIEW->value,
                self::SCORING_MIB_INFO_CREATE->value,
                self::SCORING_MIB_INFO_DELETE->value,
            ],
            PermissionGroupEnum::SCORING_TAX_INFO->value => [
                self::SCORING_TAX_INFO_VIEW_ALL->value,
                self::SCORING_TAX_INFO_VIEW->value,
                self::SCORING_TAX_INFO_CREATE->value,
                self::SCORING_TAX_INFO_DELETE->value,
            ],
            PermissionGroupEnum::SCORING_HUMO->value => [
                self::SCORING_HUMO_VIEW_ALL->value,
                self::SCORING_HUMO_VIEW->value,
                self::SCORING_HUMO_CREATE->value,
                self::SCORING_HUMO_DELETE->value,
            ],
            PermissionGroupEnum::SCORING_UZCARD->value => [
                self::SCORING_UZCARD_VIEW_ALL->value,
                self::SCORING_UZCARD_VIEW->value,
                self::SCORING_UZCARD_CREATE->value,
                self::SCORING_UZCARD_DELETE->value,
            ],
            PermissionGroupEnum::SCORING_ROYXAT_CREDIT_HISTORY->value => [
                self::SCORING_ROYXAT_CREDIT_VIEW_ALL->value,
            ],
            PermissionGroupEnum::PROFILE_EDIT->value => [
                self::PROFILE_EDIT->value,
                self::PROFILE_CHANGE_PASSWORD->value,
                self::PROFILE_CHANGE_LANGUAGE->value,
                self::PROFILE_CHANGE_AVATAR->value,
                self::PROFILE_DEVICES->value,
                self::PROFILE_REVOKE_DEVICE->value,
                self::PROFILE_CHANGE_THEME->value,
            ],
            PermissionGroupEnum::BALANCE_FUND->value => [
                self::BALANCE_FUND_VIEW_ALL->value,
                self::BALANCE_FUND_VIEW->value,
                self::BALANCE_FUND_CONFIRM->value,
                self::BALANCE_FUND_DELETE->value,
                self::BALANCE_FUND_CREATE->value,
            ],
            PermissionGroupEnum::BALANCE_CHARGE->value => [
                self::BALANCE_CHARGE_VIEW_ALL->value,
                self::BALANCE_CHARGE_VIEW->value,
                self::BALANCE_CHARGE_CONFIRM->value,
                self::BALANCE_CHARGE_DELETE->value,
                self::BALANCE_CHARGE_CREATE->value,
            ],
            PermissionGroupEnum::ACTIVITY_LOG->value => [
                self::ACTIVITY_LOG_VIEW->value,
                self::ACTIVITY_LOG_VIEW_ALL->value,
                self::ACTIVITY_LOG_VIEW_OWN->value,
            ],
            PermissionGroupEnum::ECOMMERCE->value => [
                self::PRODUCT_CATEGORY_CREATE->value,
                self::PRODUCT_CATEGORY_EDIT->value,
                self::PRODUCT_CATEGORY_DELETE->value,
                self::PRODUCT_CATEGORY_VIEW_ALL->value,
                self::PRODUCT_CATEGORY_VIEW->value,
                self::BANNER_CREATE->value,
                self::BANNER_EDIT->value,
                self::BANNER_DELETE->value,
                self::BANNER_VIEW_ALL->value,
                self::BANNER_VIEW->value,
                self::PRODUCT_BRAND_CREATE->value,
                self::PRODUCT_BRAND_EDIT->value,
                self::PRODUCT_BRAND_DELETE->value,
                self::PRODUCT_BRAND_VIEW->value,
                self::PRODUCT_BRAND_VIEW_ALL->value,
                self::PRODUCT_OPTION_VALUE_CREATE->value,
                self::PRODUCT_OPTION_VALUE_EDIT->value,
                self::PRODUCT_OPTION_VALUE_DELETE->value,
                self::PRODUCT_OPTION_VALUE_VIEW->value,
                self::PRODUCT_OPTION_VALUE_VIEW_ALL->value,
                self::PRODUCT_OPTION_GROUP_CREATE->value,
                self::PRODUCT_OPTION_GROUP_EDIT->value,
                self::PRODUCT_OPTION_GROUP_DELETE->value,
                self::PRODUCT_OPTION_GROUP_VIEW->value,
                self::PRODUCT_OPTION_GROUP_VIEW_ALL->value,
                self::PRODUCT_OPTION_CREATE->value,
                self::PRODUCT_OPTION_EDIT->value,
                self::PRODUCT_OPTION_VIEW_ALL->value,
                self::PRODUCT_OPTION_VIEW->value,
                self::PRODUCT_OPTION_DELETE->value,
                self::PRODUCT_VARIATION_CREATE->value,
                self::PRODUCT_VARIATION_EDIT->value,
                self::PRODUCT_VARIATION_DELETE->value,
                self::PRODUCT_VARIATION_VIEW->value,
                self::PRODUCT_VARIATION_VIEW_ALL->value,
                self::PRODUCT_JOIN_OPTION_CREATE->value,
                self::PRODUCT_JOIN_OPTION_EDIT->value,
                self::PRODUCT_JOIN_OPTION_DELETE->value,
                self::PRODUCT_JOIN_OPTION_VIEW->value,
                self::PRODUCT_JOIN_OPTION_VIEW_ALL->value,
                self::PRODUCT_CREATE->value,
                self::PRODUCT_EDIT->value,
                self::PRODUCT_DELETE->value,
                self::PRODUCT_VIEW->value,
                self::PRODUCT_VIEW_ALL->value,
                self::ORDER_STATUS_CREATE->value,
                self::ORDER_STATUS_EDIT->value,
                self::ORDER_STATUS_DELETE->value,
                self::ORDER_STATUS_VIEW->value,
                self::ORDER_STATUS_VIEW_ALL->value,
                self::ORDERS_VIEW->value,
                self::ORDERS_VIEW_ALL->value,
                self::ORDER_ITEMS_VIEW_ALL->value,
                self::USERS_VIEW_ALL->value,
                self::USERS_VIEW->value,
            ],
        ];
    }

    public static function getFeature(PermissionEnum $permission): FeatureEnum
    {
        return match ($permission) {
            self::PROFILE_EDIT,
            self::PROFILE_CHANGE_PASSWORD,
            self::PROFILE_CHANGE_LANGUAGE,
            self::PROFILE_CHANGE_AVATAR,
            self::PROFILE_DEVICES,
            self::PROFILE_REVOKE_DEVICE => FeatureEnum::PROFILE_EDIT,

            self::BALANCE_FUND_VIEW_ALL,
            self::BALANCE_FUND_VIEW,
            self::BALANCE_FUND_CONFIRM,
            self::BALANCE_FUND_DELETE,
            self::BALANCE_FUND_CREATE => FeatureEnum::BALANCE_FUND,

            self::BALANCE_CHARGE_VIEW_ALL,
            self::BALANCE_CHARGE_VIEW,
            self::BALANCE_CHARGE_CONFIRM,
            self::BALANCE_CHARGE_DELETE,
            self::BALANCE_CHARGE_CREATE => FeatureEnum::BALANCE_CHARGE,

            self::ADMIN_VIEW_ALL,
            self::ADMIN_VIEW,
            self::ADMIN_CREATE,
            self::ADMIN_EDIT,
            self::ADMIN_DELETE,
            self::ADMIN_CHANGE_PASSWORD,
            self::ADMIN_CHANGE_AVATAR,
            self::ADMIN_CHANGE_ROLE,
            self::ADMIN_DEVICES,
            self::ADMIN_REVOKE_DEVICE, => FeatureEnum::USER_MANAGEMENT,

            self::ADMIN_CHANGE_THEME,
            self::PROFILE_CHANGE_THEME => FeatureEnum::THEME_MANAGEMENT,

            self::ROLE_VIEW_ALL,
            self::ROLE_VIEW,
            self::ROLE_CREATE,
            self::ROLE_EDIT,
            self::ROLE_DELETE,
            self::ROLE_PERMISSIONS => FeatureEnum::ROLE_MANAGEMENT,

            self::CUSTOMER_VIEW_ALL,
            self::CUSTOMER_VIEW,
            self::CUSTOMER_VIEW_INSTALLMENTS,
            self::CUSTOMER_CREATE,
            self::CUSTOMER_EDIT,
            self::CUSTOMER_CARD_VIEW,
            self::CUSTOMER_CARD_EDIT,
            self::CUSTOMER_CARD_REMOVE,
            self::CUSTOMER_INFO_BY_PASSPORT,
            self::CUSTOMER_TAG_VIEW,
            self::CUSTOMER_TAG_ATTACH,
            self::CUSTOMER_TAG_DETACH,
            self::TAG_VIEW_ALL,
            self::TAG_VIEW,
            self::TAG_CREATE,
            self::TAG_EDIT,
            self::TAG_DELETE => FeatureEnum::CUSTOMER_MANAGEMENT,

            self::CALCULATE_INSTALLMENT,
            self::CALCULATOR_EXPORT => FeatureEnum::CALCULATOR_INSTALLMENT,

            self::INSTALLMENT_CREATE_TRANSACTION,
            self::INSTALLMENT_VIEW_ALL,
            self::INSTALLMENT_VIEW,
            self::INSTALLMENT_VIEW_STATISTICS,
            self::INSTALLMENT_CREATE,
            self::INSTALLMENT_START,
            self::INSTALLMENT_CANCEL,
            self::INSTALLMENT_FREEZE,
            self::INSTALLMENT_FINISH,
            self::INSTALLMENT_CUSTOMER_LIST,
            self::INSTALLMENT_EDIT,
            self::INSTALLMENT_DELETE,

            self::DASHBOARD_INFO_VIEW => FeatureEnum::INSTALLMENT_MANAGEMENT,

            self::INSTALLMENT_ATTACH_CARD,
            self::INSTALLMENT_DETACH_CARD => FeatureEnum::INSTALLMENT_AUTO_CHARGE,

            self::INSTALLMENT_ATTACH_GUARANTOR,
            self::INSTALLMENT_UPDATE_GUARANTOR,
            self::INSTALLMENT_DETACH_GUARANTOR => FeatureEnum::INSTALLMENT_GUARANTOR,

            self::INSTALLMENT_CREATE_COMMENT,
            self::INSTALLMENT_UPDATE_COMMENT,
            self::INSTALLMENT_DELETE_COMMENT => FeatureEnum::INSTALLMENT_COMMENT,

            self::INSTALLMENT_VIEW_DOCUMENT,
            self::INSTALLMENT_UPLOAD_DOCUMENT,
            self::INSTALLMENT_DELETE_DOCUMENT => FeatureEnum::INSTALLMENT_DOCUMENT,

            self::TARIFF_VIEW_ALL,
            self::TARIFF_VIEW,
            self::TARIFF_EDIT,
            self::TARIFF_DELETE,
            self::TARIFF_CREATE => FeatureEnum::TARIFF_MANAGEMENT,

            self::TRANSACTION_ATTEMPT_VIEW_ALL,
            self::TRANSACTION_VIEW_ALL,
            self::TRANSACTION_VIEW,
            self::TRANSACTION_CREATE,
            self::TRANSACTION_EDIT,
            self::TRANSACTION_REVERSE,
            self::TRANSACTION_DELETE => FeatureEnum::TRANSACTION_MANAGEMENT,

            self::TRANSACTION_TYPE_VIEW_ALL,
            self::TRANSACTION_TYPE_VIEW,
            self::TRANSACTION_TYPE_CREATE,
            self::TRANSACTION_TYPE_EDIT,
            self::TRANSACTION_TYPE_DELETE => FeatureEnum::TRANSACTION_TYPE_MANAGEMENT,

            self::BRANCH_VIEW_ALL,
            self::BRANCH_VIEW,
            self::BRANCH_EDIT,
            self::BRANCH_DELETE,
            self::BRANCH_CREATE,
            self::STATISTICS_BY_BRANCHES => FeatureEnum::BRANCH_MANAGEMENT,

            self::SETTINGS_EDIT => FeatureEnum::COMPANY_MANAGEMENT,

            self::SCORING_CREDIT_HISTORY_VIEW_ALL,
            self::SCORING_CREDIT_HISTORY_VIEW,
            self::SCORING_CREDIT_HISTORY_CREATE,
            self::SCORING_CREDIT_HISTORY_DELETE => FeatureEnum::SCORING_CREDIT_HISTORY,

            self::SCORING_INPS_HISTORY_VIEW_ALL,
            self::SCORING_INPS_HISTORY_VIEW,
            self::SCORING_INPS_HISTORY_CREATE,
            self::SCORING_INPS_HISTORY_DELETE => FeatureEnum::SCORING_INPS_HISTORY,

            self::SCORING_PENSION_INFO_VIEW_ALL,
            self::SCORING_PENSION_INFO_VIEW,
            self::SCORING_PENSION_INFO_CREATE,
            self::SCORING_PENSION_INFO_DELETE => FeatureEnum::SCORING_PENSION_INFO,

            self::SCORING_MIB_INFO_VIEW_ALL,
            self::SCORING_MIB_INFO_VIEW,
            self::SCORING_MIB_INFO_CREATE,
            self::SCORING_MIB_INFO_DELETE => FeatureEnum::SCORING_MIB_INFO,

            self::SCORING_TAX_INFO_VIEW_ALL,
            self::SCORING_TAX_INFO_VIEW,
            self::SCORING_TAX_INFO_CREATE,
            self::SCORING_TAX_INFO_DELETE => FeatureEnum::SCORING_TAX_INFO,

            self::SCORING_HUMO_VIEW_ALL,
            self::SCORING_HUMO_VIEW,
            self::SCORING_HUMO_CREATE,
            self::SCORING_HUMO_DELETE, => FeatureEnum::SCORING_HUMO,

            self::SCORING_UZCARD_VIEW_ALL,
            self::SCORING_UZCARD_VIEW,
            self::SCORING_UZCARD_CREATE,
            self::SCORING_UZCARD_DELETE => FeatureEnum::SCORING_UZCARD,

            self::SCORING_ROYXAT_CREDIT_VIEW_ALL => FeatureEnum::SCORING_ROYXAT_CREDIT_HISTORY,

            self::ACTIVITY_LOG_VIEW,
            self::ACTIVITY_LOG_VIEW_ALL,
            self::ACTIVITY_LOG_VIEW_OWN => FeatureEnum::ACTIVITY_LOG,

            self::PRODUCT_CATEGORY_CREATE,
            self::PRODUCT_CATEGORY_EDIT,
            self::PRODUCT_CATEGORY_DELETE,
            self::PRODUCT_CATEGORY_VIEW,
            self::PRODUCT_CATEGORY_VIEW_ALL,
            self::BANNER_CREATE,
            self::BANNER_EDIT,
            self::BANNER_DELETE,
            self::BANNER_VIEW_ALL,
            self::BANNER_VIEW,
            self::PRODUCT_BRAND_CREATE,
            self::PRODUCT_BRAND_EDIT,
            self::PRODUCT_BRAND_DELETE,
            self::PRODUCT_BRAND_VIEW,
            self::PRODUCT_BRAND_VIEW_ALL,
            self::PRODUCT_OPTION_VALUE_CREATE,
            self::PRODUCT_OPTION_VALUE_EDIT,
            self::PRODUCT_OPTION_VALUE_DELETE,
            self::PRODUCT_OPTION_VALUE_VIEW,
            self::PRODUCT_OPTION_VALUE_VIEW_ALL,
            self::PRODUCT_OPTION_GROUP_CREATE,
            self::PRODUCT_OPTION_GROUP_EDIT,
            self::PRODUCT_OPTION_GROUP_DELETE,
            self::PRODUCT_OPTION_GROUP_VIEW,
            self::PRODUCT_OPTION_GROUP_VIEW_ALL,
            self::PRODUCT_OPTION_CREATE,
            self::PRODUCT_OPTION_EDIT,
            self::PRODUCT_OPTION_VIEW_ALL,
            self::PRODUCT_OPTION_VIEW,
            self::PRODUCT_OPTION_DELETE,
            self::PRODUCT_VARIATION_CREATE,
            self::PRODUCT_VARIATION_EDIT,
            self::PRODUCT_VARIATION_DELETE,
            self::PRODUCT_VARIATION_VIEW,
            self::PRODUCT_VARIATION_VIEW_ALL,
            self::PRODUCT_JOIN_OPTION_CREATE,
            self::PRODUCT_JOIN_OPTION_EDIT,
            self::PRODUCT_JOIN_OPTION_DELETE,
            self::PRODUCT_JOIN_OPTION_VIEW,
            self::PRODUCT_JOIN_OPTION_VIEW_ALL,
            self::PRODUCT_CREATE,
            self::PRODUCT_EDIT,
            self::PRODUCT_DELETE,
            self::PRODUCT_VIEW,
            self::PRODUCT_VIEW_ALL,
            self::ORDER_STATUS_CREATE,
            self::ORDER_STATUS_EDIT,
            self::ORDER_STATUS_DELETE,
            self::ORDER_STATUS_VIEW,
            self::ORDER_STATUS_VIEW_ALL,
            self::ORDERS_VIEW,
            self::ORDERS_VIEW_ALL,
            self::ORDER_ITEMS_VIEW_ALL,
            self::USERS_VIEW_ALL,
            self::USERS_VIEW => FeatureEnum::ECOMMERCE,
        };
    }

    public function getMyFeature(): FeatureEnum
    {
        return self::getFeature($this);
    }

    public function isRelatedToCompanyStatus(): bool
    {
        return match ($this) {
            self::BALANCE_FUND_DELETE,

            self::BALANCE_CHARGE_DELETE,
            self::BALANCE_CHARGE_CREATE,
            self::BALANCE_CHARGE_CONFIRM,

            self::ADMIN_CREATE,
            self::ADMIN_EDIT,
            self::ADMIN_DELETE,
            self::ADMIN_CHANGE_PASSWORD,
            self::ADMIN_CHANGE_AVATAR,
            self::ADMIN_CHANGE_ROLE,
            self::ADMIN_CHANGE_THEME,

            self::ROLE_CREATE,
            self::ROLE_EDIT,
            self::ROLE_DELETE,
            self::ROLE_PERMISSIONS,

            self::CUSTOMER_CREATE,
            self::CUSTOMER_EDIT,
            self::CUSTOMER_CARD_EDIT,
            self::CUSTOMER_CARD_REMOVE,

            self::CALCULATE_INSTALLMENT,
            self::CALCULATOR_EXPORT,

            self::INSTALLMENT_START,
            self::INSTALLMENT_CANCEL,
            self::INSTALLMENT_FREEZE,
            self::INSTALLMENT_FINISH,
            self::INSTALLMENT_CREATE,
            self::INSTALLMENT_EDIT,
            self::INSTALLMENT_DELETE,
            self::INSTALLMENT_ATTACH_CARD,
            self::INSTALLMENT_DETACH_CARD,
            self::INSTALLMENT_ATTACH_GUARANTOR,
            self::INSTALLMENT_UPDATE_GUARANTOR,
            self::INSTALLMENT_DETACH_GUARANTOR,
            self::INSTALLMENT_CREATE_COMMENT,
            self::INSTALLMENT_CREATE_TRANSACTION,
            self::INSTALLMENT_UPDATE_COMMENT,
            self::INSTALLMENT_DELETE_COMMENT,

            self::INSTALLMENT_VIEW_DOCUMENT,
            self::INSTALLMENT_UPLOAD_DOCUMENT,
            self::INSTALLMENT_DELETE_DOCUMENT,

            self::TARIFF_EDIT,
            self::TARIFF_DELETE,
            self::TARIFF_CREATE,

            self::TRANSACTION_CREATE,
            self::TRANSACTION_EDIT,
            self::TRANSACTION_DELETE,
            self::TRANSACTION_REVERSE,

            self::TRANSACTION_TYPE_CREATE,
            self::TRANSACTION_TYPE_EDIT,
            self::TRANSACTION_TYPE_DELETE,

            self::BRANCH_EDIT,
            self::BRANCH_DELETE,
            self::BRANCH_CREATE,
            self::STATISTICS_BY_BRANCHES,

            self::SETTINGS_EDIT,

            self::SCORING_CREDIT_HISTORY_CREATE,
            self::SCORING_CREDIT_HISTORY_DELETE,

            self::SCORING_INPS_HISTORY_CREATE,
            self::SCORING_INPS_HISTORY_DELETE,

            self::SCORING_PENSION_INFO_CREATE,
            self::SCORING_PENSION_INFO_DELETE,

            self::SCORING_MIB_INFO_CREATE,
            self::SCORING_MIB_INFO_DELETE,

            self::SCORING_TAX_INFO_CREATE,
            self::SCORING_TAX_INFO_DELETE,

            self::SCORING_HUMO_CREATE,
            self::SCORING_HUMO_DELETE,

            self::SCORING_UZCARD_CREATE,
            self::SCORING_UZCARD_DELETE => true,

            default => false,
        };
    }
}
