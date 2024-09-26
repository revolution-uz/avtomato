<?php

namespace App\Enums;

enum SmsTemplateEnum: int
{
    case AUTO_CHARGE = 1;
    case CARD_CONFIRMATION = 2;
    case CHARGE = 3;
    case DEBT = 4;

    public function getDefaultTemplate(): string
    {
        return match ($this) {
            self::AUTO_CHARGE => ':pan kartadan :amount so\'m N:credit_number shartnomaga asosan :company ga yechildi. Ma\'lumot uchun: :phone',
            self::CARD_CONFIRMATION => 'Avtomato tizimida kartangizni muddatli to\'lovga biriktirish tasdiqlash kodi-:code',
            self::CHARGE => ':amount so\'m N:credit_number shartnomaga asosan :company ga to\'lov qilindi. Ma\'lumot uchun: :phone',
            self::DEBT => 'N:credit_number shartnomaga asosan :company ga joriy oyda :amount so\'m qarzinggiz bor. Ma\'lumot uchun: :phone',
        };
    }

    public function getDefaultDescription(): ?string
    {
        return match ($this) {
            self::AUTO_CHARGE => 'Avtomatlashtirilgan to\'lov bildirishnomalari uchun ishlatiladigan shablonni ifodalaydi.',
            self::CARD_CONFIRMATION => 'Karta ma\'lumotlarini tasdiqlovchi SMS xabarlar uchun ishlatiladigan shablonni ifodalaydi.',
            self::CHARGE => 'To\'lov bildirishnomalari uchun ishlatiladigan shablonni ifodalaydi.',
            self::DEBT => 'Qarzdorlik bildirishnomalari uchun ishlatiladigan shablonni ifodalaydi.',
        };
    }

    public function getExampleData(): array
    {
        return [
            'pan' => '9860 01** **** 6789',
            'amount' => 100000,
            'credit_number' => 878323,
            'company' => tenant('name'),
            'phone' => tenant('owner_phone'),
            'code' => 232432,
        ];
    }
}
