<?php

namespace App\Enums\Company\Rbac;

use App\Enums\Company\FeatureEnum;
use App\Models\Company\Feature;

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

    // Manage Users
    case USER_VIEW_ALL = 'user_view_all';
    case USER_VIEW = 'user_view';
    case USER_CREATE = 'user_create';
    case USER_EDIT = 'user_edit';
    case USER_DELETE = 'user_delete';
    case USER_CHANGE_PASSWORD = 'user_change_password';
    case USER_CHANGE_AVATAR = 'user_change_avatar';
    case USER_CHANGE_ROLE = 'user_change_role';
    case USER_DEVICES = 'user_devices';
    case USER_REVOKE_DEVICE = 'user_revoke_device';

    // Manage Themes
    case PROFILE_CHANGE_THEME = 'profile_change_theme';
    case USER_CHANGE_THEME = 'user_change_theme';

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
    case CUSTOMER_VIEW_ALL = 'customer_view_all';
    case CUSTOMER_VIEW = 'customer_view';

    // Manage Customer Cards
    case CUSTOMER_CARDS_VIEW_ALL = 'customer_cards_view_all';
    case CUSTOMER_CARDS_VIEW = 'customer_cards_view';
    case CUSTOMER_CARDS_CREATE = 'customer_cards_create';
    case CUSTOMER_CARDS_EDIT = 'customer_cards_edit';
    case CUSTOMER_CARDS_DELETE = 'customer_cards_delete';

    // Get static information
    case INFO_SETTINGS = 'info_settings';
    case INFO_REGIONS = 'info_regions';
    case INFO_DISTRICTS = 'info_districts';
    case INFO_MAHALLAS = 'info_mahallas';

    // Calculator installment
    case CALCULATE_INSTALLMENT = 'calculate_installment';
    case CALCULATOR_EXPORT = 'calculator_export';

    // Manage installment
    case INSTALLMENT_VIEW_ALL = 'installment_view_all';
    case INSTALLMENT_VIEW = 'installment_view';
    case INSTALLMENT_CREATE = 'installment_create';
    case INSTALLMENT_EDIT = 'installment_edit';
    case INSTALLMENT_DELETE = 'installment_delete';
    case INSTALLMENT_ATTACH_CARD = 'installment_attach_card';
    case INSTALLMENT_DETACH_CARD = 'installment_detach_card';
    case INSTALLMENT_ATTACH_GUARANTOR = 'installment_attach_guarantor';
    case INSTALLMENT_UPDATE_GUARANTOR = 'installment_update_guarantor';
    case INSTALLMENT_DETACH_GUARANTOR = 'installment_detach_guarantor';

    // Manage tariffs
    case TARIFF_VIEW_ALL = 'tariff_view_all';
    case TARIFF_VIEW = 'tariff_view';
    case TARIFF_EDIT = 'tariff_edit';
    case TARIFF_DELETE = 'tariff_delete';
    case TARIFF_CREATE = 'tariff_create';

    // Manage transactions
    case TRANSACTION_VIEW_ALL = 'transaction_view_all';
    case TRANSACTION_VIEW = 'transaction_view';
    case TRANSACTION_CREATE = 'transaction_create';
    case TRANSACTION_EDIT = 'transaction_edit';
    case TRANSACTION_DELETE = 'transaction_delete';

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

            self::USER_VIEW_ALL,
            self::USER_VIEW,
            self::USER_CREATE,
            self::USER_EDIT,
            self::USER_DELETE,
            self::USER_CHANGE_PASSWORD,
            self::USER_CHANGE_AVATAR,
            self::USER_CHANGE_ROLE,
            self::USER_DEVICES,
            self::USER_REVOKE_DEVICE,
            self::USER_CHANGE_THEME,

            self::CUSTOMER_VIEW_ALL,
            self::CUSTOMER_VIEW,
            self::CUSTOMER_CREATE,
            self::CUSTOMER_INFO_BY_PASSPORT,

            self::CUSTOMER_CARDS_VIEW_ALL,
            self::CUSTOMER_CARDS_VIEW,
            self::CUSTOMER_CARDS_CREATE,
            self::CUSTOMER_CARDS_EDIT,
            self::CUSTOMER_CARDS_DELETE,

            self::ROLE_VIEW_ALL,
            self::ROLE_VIEW,
            self::ROLE_CREATE,
            self::ROLE_EDIT,
            self::ROLE_DELETE,
            self::ROLE_PERMISSIONS,

            self::INFO_SETTINGS,
            self::INFO_REGIONS,
            self::INFO_DISTRICTS,
            self::INFO_MAHALLAS,

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

            self::INSTALLMENT_VIEW_ALL => false,

            // branch related
            self::INSTALLMENT_VIEW,
            self::INSTALLMENT_CREATE,
            self::INSTALLMENT_EDIT,
            self::INSTALLMENT_DELETE,
            self::INSTALLMENT_ATTACH_CARD,
            self::INSTALLMENT_DETACH_CARD,
            self::INSTALLMENT_ATTACH_GUARANTOR,
            self::INSTALLMENT_UPDATE_GUARANTOR,
            self::INSTALLMENT_DETACH_GUARANTOR,

            self::TRANSACTION_VIEW_ALL,
            self::TRANSACTION_VIEW,
            self::TRANSACTION_CREATE,
            self::TRANSACTION_EDIT,
            self::TRANSACTION_DELETE => true,
        };
    }

    public static function list(): array
    {
        return [
            PermissionGroupEnum::USER_MANAGEMENT->value => [
                self::USER_VIEW_ALL->value,
                self::USER_VIEW->value,
                self::USER_CREATE->value,
                self::USER_EDIT->value,
                self::USER_DELETE->value,
                self::USER_CHANGE_PASSWORD->value,
                self::USER_CHANGE_AVATAR->value,
                self::USER_CHANGE_ROLE->value,
                self::USER_DEVICES->value,
                self::USER_REVOKE_DEVICE->value,
                self::USER_CHANGE_THEME->value,
            ],
            PermissionGroupEnum::CUSTOMER_MANAGEMENT->value => [
                self::CUSTOMER_VIEW_ALL->value,
                self::CUSTOMER_VIEW->value,
                self::CUSTOMER_CREATE->value,
                self::CUSTOMER_INFO_BY_PASSPORT->value,
            ],
            PermissionGroupEnum::CUSTOMER_CARDS_MANAGEMENT->value => [
                self::CUSTOMER_CARDS_VIEW_ALL->value,
                self::CUSTOMER_CARDS_VIEW->value,
                self::CUSTOMER_CARDS_CREATE->value,
                self::CUSTOMER_CARDS_EDIT->value,
                self::CUSTOMER_CARDS_DELETE->value,
            ],
            PermissionGroupEnum::ROLE_MANAGEMENT->value => [
                self::ROLE_VIEW_ALL->value,
                self::ROLE_VIEW->value,
                self::ROLE_CREATE->value,
                self::ROLE_EDIT->value,
                self::ROLE_DELETE->value,
                self::ROLE_PERMISSIONS->value,
            ],
            PermissionGroupEnum::GET_STATIC_INFORMATION->value => [
                self::INFO_SETTINGS->value,
                self::INFO_REGIONS->value,
                self::INFO_DISTRICTS->value,
                self::INFO_MAHALLAS->value,
            ],
            PermissionGroupEnum::CALCULATOR_INSTALLMENT->value => [
                self::CALCULATE_INSTALLMENT->value,
                self::CALCULATOR_EXPORT->value,
            ],
            PermissionGroupEnum::INSTALLMENT_MANAGEMENT->value => [
                self::INSTALLMENT_VIEW_ALL->value,
                self::INSTALLMENT_VIEW->value,
                self::INSTALLMENT_CREATE->value,
                self::INSTALLMENT_EDIT->value,
                self::INSTALLMENT_DELETE->value,
                self::INSTALLMENT_ATTACH_CARD->value,
                self::INSTALLMENT_DETACH_CARD->value,
                self::INSTALLMENT_ATTACH_GUARANTOR->value,
                self::INSTALLMENT_UPDATE_GUARANTOR->value,
                self::INSTALLMENT_DETACH_GUARANTOR->value,
            ],
            PermissionGroupEnum::TARIFF_MANAGEMENT->value => [
                self::TARIFF_VIEW_ALL->value,
                self::TARIFF_VIEW->value,
                self::TARIFF_EDIT->value,
                self::TARIFF_DELETE->value,
                self::TARIFF_CREATE->value,
            ],
            PermissionGroupEnum::TRANSACTION_MANAGEMENT->value => [
                self::TRANSACTION_VIEW_ALL->value,
                self::TRANSACTION_VIEW->value,
                self::TRANSACTION_CREATE->value,
                self::TRANSACTION_EDIT->value,
                self::TRANSACTION_DELETE->value,
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
            PermissionGroupEnum::PROFILE_EDIT->value => [
                self::PROFILE_EDIT->value,
                self::PROFILE_CHANGE_PASSWORD->value,
                self::PROFILE_CHANGE_LANGUAGE->value,
                self::PROFILE_CHANGE_AVATAR->value,
                self::PROFILE_DEVICES->value,
                self::PROFILE_REVOKE_DEVICE->value,
                self::PROFILE_CHANGE_THEME->value,
            ],
        ];
    }

    public static function getFeatureId(PermissionEnum $permission): int
    {
        return match ($permission) {
            self::PROFILE_EDIT,
            self::PROFILE_CHANGE_PASSWORD,
            self::PROFILE_CHANGE_LANGUAGE,
            self::PROFILE_CHANGE_AVATAR,
            self::PROFILE_DEVICES,
            self::PROFILE_REVOKE_DEVICE => Feature::findByName(FeatureEnum::PROFILE_EDIT)->id,

            self::USER_VIEW_ALL,
            self::USER_VIEW,
            self::USER_CREATE,
            self::USER_EDIT,
            self::USER_DELETE,
            self::USER_CHANGE_PASSWORD,
            self::USER_CHANGE_AVATAR,
            self::USER_CHANGE_ROLE,
            self::USER_DEVICES,
            self::USER_REVOKE_DEVICE, => Feature::findByName(FeatureEnum::USER_MANAGEMENT)->id,

            self::USER_CHANGE_THEME,
            self::PROFILE_CHANGE_THEME => Feature::findByName(FeatureEnum::THEME_MANAGEMENT)->id,

            self::ROLE_VIEW_ALL,
            self::ROLE_VIEW,
            self::ROLE_CREATE,
            self::ROLE_EDIT,
            self::ROLE_DELETE,
            self::ROLE_PERMISSIONS => Feature::findByName(FeatureEnum::ROLE_MANAGEMENT)->id,

            self::CUSTOMER_VIEW_ALL,
            self::CUSTOMER_VIEW,
            self::CUSTOMER_CREATE,
            self::CUSTOMER_INFO_BY_PASSPORT => Feature::findByName(FeatureEnum::CUSTOMER_MANAGEMENT)->id,

            self::CUSTOMER_CARDS_VIEW_ALL,
            self::CUSTOMER_CARDS_VIEW,
            self::CUSTOMER_CARDS_CREATE,
            self::CUSTOMER_CARDS_EDIT,
            self::CUSTOMER_CARDS_DELETE => Feature::findByName(FeatureEnum::CUSTOMER_CARDS_MANAGEMENT)->id,

            self::INFO_SETTINGS,
            self::INFO_REGIONS,
            self::INFO_DISTRICTS,
            self::INFO_MAHALLAS => Feature::findByName(FeatureEnum::GET_STATIC_INFORMATION)->id,

            self::CALCULATE_INSTALLMENT,
            self::CALCULATOR_EXPORT => Feature::findByName(FeatureEnum::CALCULATOR_INSTALLMENT)->id,

            self::INSTALLMENT_VIEW_ALL,
            self::INSTALLMENT_VIEW,
            self::INSTALLMENT_CREATE,
            self::INSTALLMENT_EDIT,
            self::INSTALLMENT_DELETE => Feature::findByName(FeatureEnum::INSTALLMENT_MANAGEMENT)->id,

            self::INSTALLMENT_ATTACH_CARD,
            self::INSTALLMENT_DETACH_CARD => Feature::findByName(FeatureEnum::INSTALLMENT_AUTO_CHARGE)->id,

            self::INSTALLMENT_ATTACH_GUARANTOR,
            self::INSTALLMENT_UPDATE_GUARANTOR,
            self::INSTALLMENT_DETACH_GUARANTOR => Feature::findByName(FeatureEnum::INSTALLMENT_GUARANTOR)->id,

            self::TARIFF_VIEW_ALL,
            self::TARIFF_VIEW,
            self::TARIFF_EDIT,
            self::TARIFF_DELETE,
            self::TARIFF_CREATE => Feature::findByName(FeatureEnum::TARIFF_MANAGEMENT)->id,

            self::TRANSACTION_VIEW_ALL,
            self::TRANSACTION_VIEW,
            self::TRANSACTION_CREATE,
            self::TRANSACTION_EDIT,
            self::TRANSACTION_DELETE => Feature::findByName(FeatureEnum::TRANSACTION_MANAGEMENT)->id,

            self::TRANSACTION_TYPE_VIEW_ALL,
            self::TRANSACTION_TYPE_VIEW,
            self::TRANSACTION_TYPE_CREATE,
            self::TRANSACTION_TYPE_EDIT,
            self::TRANSACTION_TYPE_DELETE => Feature::findByName(FeatureEnum::TRANSACTION_TYPE_MANAGEMENT)->id,

            self::BRANCH_VIEW_ALL,
            self::BRANCH_VIEW,
            self::BRANCH_EDIT,
            self::BRANCH_DELETE,
            self::BRANCH_CREATE => Feature::findByName(FeatureEnum::BRANCH_MANAGEMENT)->id,

            self::SCORING_CREDIT_HISTORY_VIEW_ALL,
            self::SCORING_CREDIT_HISTORY_VIEW,
            self::SCORING_CREDIT_HISTORY_CREATE,
            self::SCORING_CREDIT_HISTORY_DELETE => Feature::findByName(FeatureEnum::SCORING_CREDIT_HISTORY)->id,

            self::SCORING_INPS_HISTORY_VIEW_ALL,
            self::SCORING_INPS_HISTORY_VIEW,
            self::SCORING_INPS_HISTORY_CREATE,
            self::SCORING_INPS_HISTORY_DELETE => Feature::findByName(FeatureEnum::SCORING_INPS_HISTORY)->id,

            self::SCORING_PENSION_INFO_VIEW_ALL,
            self::SCORING_PENSION_INFO_VIEW,
            self::SCORING_PENSION_INFO_CREATE,
            self::SCORING_PENSION_INFO_DELETE => Feature::findByName(FeatureEnum::SCORING_PENSION_INFO)->id,

            self::SCORING_MIB_INFO_VIEW_ALL,
            self::SCORING_MIB_INFO_VIEW,
            self::SCORING_MIB_INFO_CREATE,
            self::SCORING_MIB_INFO_DELETE => Feature::findByName(FeatureEnum::SCORING_MIB_INFO)->id,

            self::SCORING_TAX_INFO_VIEW_ALL,
            self::SCORING_TAX_INFO_VIEW,
            self::SCORING_TAX_INFO_CREATE,
            self::SCORING_TAX_INFO_DELETE => Feature::findByName(FeatureEnum::SCORING_TAX_INFO)->id,

            self::SCORING_HUMO_VIEW_ALL,
            self::SCORING_HUMO_VIEW,
            self::SCORING_HUMO_CREATE,
            self::SCORING_HUMO_DELETE, => Feature::findByName(FeatureEnum::SCORING_HUMO)->id,

            self::SCORING_UZCARD_VIEW_ALL,
            self::SCORING_UZCARD_VIEW,
            self::SCORING_UZCARD_CREATE,
            self::SCORING_UZCARD_DELETE => Feature::findByName(FeatureEnum::SCORING_UZCARD)->id,
        };
    }
}
