<?php

namespace App\Enums\Main;

enum SettingsEnum: string
{
    case PARTNER_APP_MAINTENANCE_MODE = 'partner_app_maintenance_mode';
    case CLIENT_API_MAINTENANCE_MODE = 'client_api_maintenance_mode';
    case PARTNER_WEB_MAINTENANCE_MODE = 'partner_web_maintenance_mode';
    case WEBSITE_MAINTENANCE_MODE = 'website_maintenance_mode';
    case PARTNER_APP_MIN_VERSION = 'partner_app_min_version';
    case CLIENT_APP_MIN_VERSION = 'client_app_min_version';
    case TELEGRAM_BOT_TOKEN = 'telegram_bot_token';
    case TELEGRAM_CHAT_ID = 'telegram_chat_id';
    case TEST_PHONE = 'test_phone';
    case TEST_OTP = 'test_otp';

    public static function getType(self $type): SettingsTypeEnum
    {
        return match ($type) {
            self::PARTNER_APP_MAINTENANCE_MODE, self::CLIENT_API_MAINTENANCE_MODE, self::PARTNER_WEB_MAINTENANCE_MODE, self::WEBSITE_MAINTENANCE_MODE => SettingsTypeEnum::BOOLEAN,
            self::PARTNER_APP_MIN_VERSION, self::CLIENT_APP_MIN_VERSION => SettingsTypeEnum::INT,
            self::TELEGRAM_BOT_TOKEN, self::TELEGRAM_CHAT_ID, self::TEST_PHONE, self::TEST_OTP => SettingsTypeEnum::STRING,
        };
    }

    public static function getDefaultValue(self $type): string|int
    {
        return match ($type) {
            self::PARTNER_APP_MAINTENANCE_MODE, self::CLIENT_API_MAINTENANCE_MODE, self::PARTNER_WEB_MAINTENANCE_MODE, self::WEBSITE_MAINTENANCE_MODE => 0,
            self::PARTNER_APP_MIN_VERSION, self::CLIENT_APP_MIN_VERSION => 1,
            self::TELEGRAM_BOT_TOKEN, self::TELEGRAM_CHAT_ID, self::TEST_PHONE, self::TEST_OTP => '',
        };
    }
}
