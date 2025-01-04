<?php

namespace App\Enums\Company;

enum SmsTemplateEnum: int
{
    case AUTO_CHARGE = 1;
    case CARD_CONFIRMATION = 2;
    case CHARGE = 3;
    case DEBT = 4;
    case ECOMMERCE_OTP = 5;
    case DEBT_10 = 6;
    case DEBT_20 = 7;

    public static function getExampleData(): array
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

    public function getDefaultTemplate(): array
    {
        return match ($this) {
            self::AUTO_CHARGE => [
                'status' => SmsTemplateStatusEnum::ACTIVE,
                'original_template' => ':pan kartadan :amount so\'m N:credit_number shartnomaga asosan :company ga yechildi. Ma\'lumot uchun: :phone',
                'description' => 'Avtomatlashtirilgan to\'lov bildirishnomalari uchun ishlatiladigan shablonni ifodalaydi.',
            ],
            self::CARD_CONFIRMATION => [
                'status' => SmsTemplateStatusEnum::ACTIVE,
                'original_template' => 'Avtomato tizimida kartangizni muddatli to\'lovga biriktirish tasdiqlash kodi-:code',
                'description' => 'Karta ma\'lumotlarini tasdiqlovchi SMS xabarlar uchun ishlatiladigan shablonni ifodalaydi.',
            ],
            self::CHARGE => [
                'status' => SmsTemplateStatusEnum::ACTIVE,
                'original_template' => ':amount so\'m N:credit_number shartnomaga asosan :company ga to\'lov qilindi. Ma\'lumot uchun: :phone',
                'description' => 'To\'lov bildirishnomalari uchun ishlatiladigan shablonni ifodalaydi.',
            ],
            self::DEBT => [
                'status' => SmsTemplateStatusEnum::ACTIVE,
                'original_template' => 'N:credit_number shartnomaga asosan :company ga joriy oyda :amount so\'m qarzinggiz bor. Ma\'lumot uchun: :phone',
                'original_data' => [
                    'category' => 'debt-templates',
                    'overdue_days' => 0,
                ],
                'description' => 'Qarzdorlik bildirishnomalari uchun ishlatiladigan shablonni ifodalaydi.',
            ],
            self::ECOMMERCE_OTP => [
                'status' => SmsTemplateStatusEnum::ACTIVE,
                'original_template' => ':ecommerce dan ro\'yxatdan o\'tish uchun kod: :code',
                'description' => 'Onlayn do\'kondan ro\'yxatdan o\'tish uchun ishlatiladigan shablonni ifodalaydi.',
            ],
            self::DEBT_10 => [
                'status' => SmsTemplateStatusEnum::ACTIVE,
                'original_template' => 'N:credit_number shartnomaga asosan :company ga joriy oyda :amount so\'m qarzinggiz bor. Qarzdorlik muddatidan 10 kun o\'tdi. Ma\'lumot uchun: :phone',
                'original_data' => [
                    'category' => 'debt-templates',
                    'overdue_days' => 10,
                ],
                'description' => 'Qarzdorlik bildirishnomalari uchun ishlatiladigan shablonni ifodalaydi.',
            ],
            self::DEBT_20 => [
                'status' => SmsTemplateStatusEnum::ACTIVE,
                'original_template' => 'N:credit_number shartnomaga asosan :company ga joriy oyda :amount so\'m qarzinggiz bor. Qarzdorlik muddatidan 20 kun o\'tdi. Ma\'lumot uchun: :phone',
                'original_data' => [
                    'category' => 'debt-templates',
                    'overdue_days' => 20,
                ],
                'description' => 'Qarzdorlik bildirishnomalari uchun ishlatiladigan shablonni ifodalaydi.',
            ],
        };
    }
}
