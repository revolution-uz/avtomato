<?php

namespace App\Enums\Company;

use App\Enums\Main\SettingsTypeEnum;

enum CompanySettingsEnum: string
{
    case CARD_AFTER_DUE_DAYS = 'card_after_due_days';
    case PINFL_AFTER_DUE_DAYS = 'pinfl_after_due_days';
    case GUARANTOR_AFTER_DUE_DAYS = 'guarantor_after_due_days';
    case GUARANTOR_PINFL_AFTER_DUE_DAYS = 'guarantor_pinfl_after_due_days';
    case SMS_INSTALLMENT_AUTO_CHARGE = 'sms_installment_auto_charge';
    case SMS_INSTALLMENT_NOTIFICATION = 'sms_installment_notification';
    case SMS_INSTALLMENT_PAYMENT = 'sms_installment_payment';

    public static function getType(self $type): SettingsTypeEnum
    {
        return match ($type) {
            self::CARD_AFTER_DUE_DAYS, self::PINFL_AFTER_DUE_DAYS, self::GUARANTOR_AFTER_DUE_DAYS, self::GUARANTOR_PINFL_AFTER_DUE_DAYS => SettingsTypeEnum::INT,
            self::SMS_INSTALLMENT_AUTO_CHARGE, self::SMS_INSTALLMENT_NOTIFICATION, self::SMS_INSTALLMENT_PAYMENT => SettingsTypeEnum::BOOLEAN,
        };
    }

    public static function getDefaultValue(self $type): int|bool
    {
        return match ($type) {
            self::CARD_AFTER_DUE_DAYS => 1,
            self::PINFL_AFTER_DUE_DAYS => 5,
            self::GUARANTOR_AFTER_DUE_DAYS => 10,
            self::GUARANTOR_PINFL_AFTER_DUE_DAYS => 15,
            self::SMS_INSTALLMENT_AUTO_CHARGE, self::SMS_INSTALLMENT_NOTIFICATION, self::SMS_INSTALLMENT_PAYMENT => false,
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
        };
    }
}