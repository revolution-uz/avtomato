<?php

namespace App\Enums;

enum SmsTemplateEnum: int
{
    case TEMPLATE_AUTO_CHARGE = 1;
    case TEMPLATE_CARD_CONFIRMATION = 2;

    public function getDefaultTemplate(): string
    {
        return match ($this) {
            self::TEMPLATE_AUTO_CHARGE => ':pan kartadan :amount so\'m N:credit_number shartnomaga asosan :company ga yechildi. Ma\'lumot uchun: :phone',
            self::TEMPLATE_CARD_CONFIRMATION => 'Avtomato tizimida operatsiyani tasdiqlash uchun kod: :code',
        };
    }

    public function getDefaultDescription(): ?string
    {
        return match ($this) {
            self::TEMPLATE_AUTO_CHARGE => 'Avtomatlashtirilgan to\'lov bildirishnomalari uchun ishlatiladigan shablonni ifodalaydi.',
            self::TEMPLATE_CARD_CONFIRMATION => 'Karta ma\'lumotlarini tasdiqlovchi SMS xabarlar uchun ishlatiladigan shablonni ifodalaydi.',
        };
    }

    public function getExampleData(): array
    {
        return match ($this) {
            self::TEMPLATE_AUTO_CHARGE => [
                'pan' => '9860 01** **** 6216',
                'amount' => 100000,
                'credit_number' => 878323,
                'company' => tenant('name'),
                'phone' => tenant('owner_phone'),
            ],
            self::TEMPLATE_CARD_CONFIRMATION => ['code' => 232432],
        };
    }
}
