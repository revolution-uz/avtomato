<?php

namespace App\Enums\Company;

use App\Enums\EnumMethods;
use App\Enums\Main\SettingsTypeEnum;

enum CompanySettingsEnum: string
{
    use EnumMethods;

    case CARD_AFTER_DUE_DAYS = 'card_after_due_days';
    case PINFL_AFTER_DUE_DAYS = 'pinfl_after_due_days';
    case GUARANTOR_AFTER_DUE_DAYS = 'guarantor_after_due_days';
    case GUARANTOR_PINFL_AFTER_DUE_DAYS = 'guarantor_pinfl_after_due_days';
    case SMS_INSTALLMENT_AUTO_CHARGE = 'sms_installment_auto_charge'; // auto charge notification
    case SMS_INSTALLMENT_NOTIFICATION = 'sms_installment_notification'; // charge notification
    case SMS_INSTALLMENT_PAYMENT = 'sms_installment_payment'; // debt notification
    case PINFL_CARDS_UPDATE_DAYS = 'pinfl_cards_update_days';

    // Telegram bot settings
    case TELEGRAM_BOT_ENABLED = 'telegram_bot_enabled';
    case TELEGRAM_BOT_TOKEN = 'telegram_bot_token';
    case TELEGRAM_CHAT_ID = 'telegram_chat_id';

    // Loyalty program settings
    case LOYALTY_PROGRAM_SYNC_TOKEN = 'loyalty_program_sync_token';
    case LOYALTY_PROGRAM_BALANCE_LIMIT = 'loyalty_program_balance_limit';
    case LOYALTY_PROGRAM_FIREBASE_PATH = 'loyalty_program_firebase_path';
    case LOYALTY_PROGRAM_APP_MAINTENANCE_MODE = 'lp_app_maintenance_mode';
    case LOYALTY_PROGRAM_SUPPORT_BOT_USERNAME = 'lp_support_bot_username';
    case LOYALTY_PROGRAM_SUPPORT_PHONE = 'lp_support_phone';
    case LOYALTY_PROGRAM_APP_MIN_VERSION = 'lp_app_min_version';
    case LOYALTY_PROGRAM_UPDATE_TYPE = 'lp_update_type';
    case LOYALTY_PROGRAM_INSTRUCTION_VIDEO_TYPE = 'lp_instruction_video_type';
    case LOYALTY_PROGRAM_INSTRUCTION_VIDEO_PATH = 'lp_instruction_video_path';

    // ICAN settings
    case ICAN_BRANCH_ID = 'ican_branch_id';
    case ICAN_EMPLOYEE_ID = 'ican_employee_id';

    public static function getType(self $type): SettingsTypeEnum
    {
        return match ($type) {
            self::CARD_AFTER_DUE_DAYS,
            self::PINFL_AFTER_DUE_DAYS,
            self::GUARANTOR_AFTER_DUE_DAYS,
            self::GUARANTOR_PINFL_AFTER_DUE_DAYS,
            self::LOYALTY_PROGRAM_BALANCE_LIMIT,
            self::ICAN_BRANCH_ID,
            self::ICAN_EMPLOYEE_ID,
            self::LOYALTY_PROGRAM_UPDATE_TYPE,
            self::LOYALTY_PROGRAM_INSTRUCTION_VIDEO_TYPE,
            self::PINFL_CARDS_UPDATE_DAYS => SettingsTypeEnum::INT,

            self::SMS_INSTALLMENT_AUTO_CHARGE,
            self::SMS_INSTALLMENT_NOTIFICATION,
            self::SMS_INSTALLMENT_PAYMENT,
            self::TELEGRAM_BOT_ENABLED,
            self::LOYALTY_PROGRAM_APP_MAINTENANCE_MODE => SettingsTypeEnum::BOOLEAN,

            self::LOYALTY_PROGRAM_SYNC_TOKEN,
            self::TELEGRAM_BOT_TOKEN,
            self::TELEGRAM_CHAT_ID,
            self::LOYALTY_PROGRAM_SUPPORT_BOT_USERNAME,
            self::LOYALTY_PROGRAM_SUPPORT_PHONE,
            self::LOYALTY_PROGRAM_APP_MIN_VERSION => SettingsTypeEnum::STRING,

            self::LOYALTY_PROGRAM_FIREBASE_PATH,
            self::LOYALTY_PROGRAM_INSTRUCTION_VIDEO_PATH => SettingsTypeEnum::FILE,
        };
    }

    public static function getDefaultValue(self $type): int|bool|string
    {
        return match ($type) {
            self::PINFL_CARDS_UPDATE_DAYS => 90,
            self::CARD_AFTER_DUE_DAYS, self::LOYALTY_PROGRAM_APP_MIN_VERSION, self::LOYALTY_PROGRAM_UPDATE_TYPE, self::ICAN_BRANCH_ID => 1,
            self::PINFL_AFTER_DUE_DAYS => 5,
            self::GUARANTOR_AFTER_DUE_DAYS => 10,
            self::GUARANTOR_PINFL_AFTER_DUE_DAYS => 15,
            self::SMS_INSTALLMENT_AUTO_CHARGE => true,
            self::SMS_INSTALLMENT_NOTIFICATION, self::SMS_INSTALLMENT_PAYMENT, self::TELEGRAM_BOT_ENABLED, self::LOYALTY_PROGRAM_APP_MAINTENANCE_MODE => false,
            self::TELEGRAM_BOT_TOKEN,
            self::TELEGRAM_CHAT_ID,
            self::LOYALTY_PROGRAM_SYNC_TOKEN,
            self::LOYALTY_PROGRAM_BALANCE_LIMIT,
            self::LOYALTY_PROGRAM_FIREBASE_PATH,
            self::ICAN_EMPLOYEE_ID,
            self::LOYALTY_PROGRAM_SUPPORT_BOT_USERNAME,
            self::LOYALTY_PROGRAM_SUPPORT_PHONE,
            self::LOYALTY_PROGRAM_INSTRUCTION_VIDEO_PATH => '',
            self::LOYALTY_PROGRAM_INSTRUCTION_VIDEO_TYPE => SettingsTypeEnum::FILE->value,
        };
    }

    public static function installmentSettings(): array
    {
        return [
            self::CARD_AFTER_DUE_DAYS,
            self::PINFL_AFTER_DUE_DAYS,
            self::GUARANTOR_AFTER_DUE_DAYS,
            self::GUARANTOR_PINFL_AFTER_DUE_DAYS,
            self::SMS_INSTALLMENT_AUTO_CHARGE,
            self::SMS_INSTALLMENT_NOTIFICATION,
            self::SMS_INSTALLMENT_PAYMENT,
            self::PINFL_CARDS_UPDATE_DAYS,
        ];
    }

    public static function telegramBotSettings(): array
    {
        return [
            self::TELEGRAM_BOT_ENABLED,
            self::TELEGRAM_BOT_TOKEN,
            self::TELEGRAM_CHAT_ID,
        ];
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::CARD_AFTER_DUE_DAYS => __('settings.card_after_due_days'),
            self::PINFL_AFTER_DUE_DAYS => __('settings.pinfl_after_due_days'),
            self::GUARANTOR_AFTER_DUE_DAYS => __('settings.guarantor_after_due_days'),
            self::GUARANTOR_PINFL_AFTER_DUE_DAYS => __('settings.guarantor_pinfl_after_due_days'),
            self::SMS_INSTALLMENT_AUTO_CHARGE => __('settings.sms_installment_auto_charge'),
            self::SMS_INSTALLMENT_NOTIFICATION => __('settings.sms_installment_notification'),
            self::SMS_INSTALLMENT_PAYMENT => __('settings.sms_installment_payment'),
            self::PINFL_CARDS_UPDATE_DAYS => __('settings.pinfl_cards_update_days'),
            self::TELEGRAM_BOT_ENABLED => __('settings.telegram_bot_enabled'),
            self::TELEGRAM_BOT_TOKEN => __('settings.telegram_bot_token'),
            self::TELEGRAM_CHAT_ID => __('settings.telegram_chat_id'),
            self::LOYALTY_PROGRAM_SYNC_TOKEN => __('settings.loyalty_program_sync_token'),
            self::LOYALTY_PROGRAM_BALANCE_LIMIT => __('settings.loyalty_program_card_balance_check'),
            self::LOYALTY_PROGRAM_FIREBASE_PATH => __('settings.loyalty_program_firebase_path'),
            self::ICAN_BRANCH_ID => __('settings.ican_branch_id'),
            self::ICAN_EMPLOYEE_ID => __('settings.ican_employee_id'),
            self::LOYALTY_PROGRAM_APP_MAINTENANCE_MODE => __('settings.loyalty_program_app_maintenance_mode'),
            self::LOYALTY_PROGRAM_SUPPORT_BOT_USERNAME => __('settings.loyalty_program_support_bot_username'),
            self::LOYALTY_PROGRAM_SUPPORT_PHONE => __('settings.loyalty_program_support_phone'),
            self::LOYALTY_PROGRAM_APP_MIN_VERSION => __('settings.loyalty_program_app_min_version'),
            self::LOYALTY_PROGRAM_UPDATE_TYPE => __('settings.loyalty_program_update_type'),
            self::LOYALTY_PROGRAM_INSTRUCTION_VIDEO_PATH => __('settings.loyalty_program_instruction_video_path'),
            self::LOYALTY_PROGRAM_INSTRUCTION_VIDEO_TYPE => __('settings.loyalty_program_instruction_video_type'),
        };
    }
}
