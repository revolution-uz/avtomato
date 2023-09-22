<?php

namespace App\Enums;

enum SettingsEnum: string
{
    case PARTNER_API_MAINTENANCE_MODE = 'partner_api_maintenance_mode';
    case CLIENT_API_MAINTENANCE_MODE = 'client_api_maintenance_mode';
    case PARTNER_PANEL_MAINTENANCE_MODE = 'partner_panel_maintenance_mode';
    case WEBSITE_MAINTENANCE_MODE = 'website_maintenance_mode';
    case PARTNER_APP_MIN_VERSION = 'partner_app_min_version';
    case CLIENT_APP_MIN_VERSION = 'client_app_min_version';
    case SMS_PROVIDER = 'sms_provider';

    public static function partnerParameters()
    {
        return [self::PARTNER_API_MAINTENANCE_MODE, self::PARTNER_APP_MIN_VERSION];
    }
}
