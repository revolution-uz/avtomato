<?php

namespace App\Enums;

enum SmsTemplateEnum: int
{
    case AUTO_CHARGE = 1;
    case CARD_CONFIRMATION = 2;

    public function getDefaultTemplate(): string
    {
        return match ($this) {
            self::AUTO_CHARGE => ':pan kartadan :amount so\'m N:credit_number shartnomaga asosan :company ga yechildi. Ma\'lumot uchun: :phone',
            self::CARD_CONFIRMATION => 'Avtomato tizimida kartangizni muddatli to\'lovga biriktirish tasdiqlash kodi- :code',
        };
    }

    public function getDefaultDescription(): ?string
    {
        return match ($this) {
            self::AUTO_CHARGE => 'Avtomatlashtirilgan to\'lov bildirishnomalari uchun ishlatiladigan shablonni ifodalaydi.',
            self::CARD_CONFIRMATION => 'Karta ma\'lumotlarini tasdiqlovchi SMS xabarlar uchun ishlatiladigan shablonni ifodalaydi.',
        };
    }

    public function getExampleData(): array
    {
        return match ($this) {
            self::AUTO_CHARGE => [
                'pan' => '9860 01** **** 6216',
                'amount' => 100000,
                'credit_number' => 878323,
                'company' => tenant('name'),
                'phone' => tenant('owner_phone'),
            ],
            self::CARD_CONFIRMATION => ['code' => 232432],
        };
    }
}
