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
    case TELEGRAM_BOT_ENABLED = 'telegram_bot_enabled';
    case TELEGRAM_BOT_TOKEN = 'telegram_bot_token';
    case TELEGRAM_CHAT_ID = 'telegram_chat_id';
    case LOYALTY_PROGRAM_SYNC_TOKEN = 'loyalty_program_sync_token';
    case LOYALTY_PROGRAM_BALANCE_LIMIT = 'loyalty_program_balance_limit';
    case LOYALTY_PROGRAM_FIREBASE_PATH = 'loyalty_program_firebase_path';

    public static function getType(self $type): SettingsTypeEnum
    {
        return match ($type) {
            self::CARD_AFTER_DUE_DAYS,
            self::PINFL_AFTER_DUE_DAYS,
            self::GUARANTOR_AFTER_DUE_DAYS,
            self::GUARANTOR_PINFL_AFTER_DUE_DAYS,
            self::LOYALTY_PROGRAM_BALANCE_LIMIT => SettingsTypeEnum::INT,

            self::SMS_INSTALLMENT_AUTO_CHARGE,
            self::SMS_INSTALLMENT_NOTIFICATION,
            self::SMS_INSTALLMENT_PAYMENT,
            self::TELEGRAM_BOT_ENABLED => SettingsTypeEnum::BOOLEAN,

            self::LOYALTY_PROGRAM_SYNC_TOKEN,
            self::TELEGRAM_BOT_TOKEN,
            self::TELEGRAM_CHAT_ID => SettingsTypeEnum::STRING,

            self::LOYALTY_PROGRAM_FIREBASE_PATH => SettingsTypeEnum::FILE,
        };
    }

    public static function getDefaultValue(self $type): int|bool|string
    {
        return match ($type) {
            self::CARD_AFTER_DUE_DAYS => 1,
            self::PINFL_AFTER_DUE_DAYS => 5,
            self::GUARANTOR_AFTER_DUE_DAYS => 10,
            self::GUARANTOR_PINFL_AFTER_DUE_DAYS => 15,
            self::SMS_INSTALLMENT_AUTO_CHARGE => true,
            self::SMS_INSTALLMENT_NOTIFICATION, self::SMS_INSTALLMENT_PAYMENT, self::TELEGRAM_BOT_ENABLED => false,
            self::TELEGRAM_BOT_TOKEN, self::TELEGRAM_CHAT_ID, self::LOYALTY_PROGRAM_SYNC_TOKEN, self::LOYALTY_PROGRAM_BALANCE_LIMIT, self::LOYALTY_PROGRAM_FIREBASE_PATH => '',
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
            self::TELEGRAM_BOT_ENABLED => __('settings.telegram_bot_enabled'),
            self::TELEGRAM_BOT_TOKEN => __('settings.telegram_bot_token'),
            self::TELEGRAM_CHAT_ID => __('settings.telegram_chat_id'),
            self::LOYALTY_PROGRAM_SYNC_TOKEN => __('settings.loyalty_program_sync_token'),
            self::LOYALTY_PROGRAM_BALANCE_LIMIT => __('settings.loyalty_program_card_balance_check'),
            self::LOYALTY_PROGRAM_FIREBASE_PATH => __('settings.loyalty_program_firebase_path'),
        };
    }
}
