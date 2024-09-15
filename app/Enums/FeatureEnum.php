<?php

namespace App\Enums;

enum FeatureEnum: string
{
    case PROFILE_EDIT = 'profile_edit';
    case BALANCE_FUND = 'balance_fund';
    case BALANCE_CHARGE = 'balance_charge';
    case USER_MANAGEMENT = 'user_management';
    case THEME_MANAGEMENT = 'theme_management';
    case CUSTOMER_MANAGEMENT = 'customer_management';
    case CUSTOMER_CARDS_MANAGEMENT = 'customer_cards_management';
    case CALCULATOR_INSTALLMENT = 'calculator_installment';
    case INSTALLMENT_MANAGEMENT = 'installment_management';
    case SMS = 'sms';
    case INSTALLMENT_AUTO_CHARGE = 'installment_auto_charge';
    case INSTALLMENT_GUARANTOR = 'installment_guarantor';
    case INSTALLMENT_COMMENT = 'installment_comment';
    case INSTALLMENT_DOCUMENT = 'installment_document';
    case INSTALLMENT_AUTO_CHARGE_BY_PINFL = 'installment_auto_charge_by_pinfl';
    case TARIFF_MANAGEMENT = 'tariff_management';
    case TRANSACTION_MANAGEMENT = 'transaction_management';
    case TRANSACTION_TYPE_MANAGEMENT = 'transaction_type_management';
    case BRANCH_MANAGEMENT = 'branch_management';
    case COMPANY_MANAGEMENT = 'company_management';
    case ROLE_MANAGEMENT = 'role_management';
    case SCORING_CREDIT_HISTORY = 'scoring_credit_history';
    case SCORING_INPS_HISTORY = 'scoring_inps_history';
    case SCORING_PENSION_INFO = 'scoring_pension_info';
    case SCORING_MIB_INFO = 'scoring_mib_info';
    case SCORING_TAX_INFO = 'scoring_tax_info';
    case SCORING_HUMO = 'scoring_humo';
    case SCORING_UZCARD = 'scoring_uzcard';
    case ACTIVITY_LOG = 'activity_log';
    case ATMOS_CARD_PAY = 'atmos_card_pay';
    case ATMOS_PINFL_PAY = 'atmos_pinfl_pay';
    case UNIRED_PAY = 'unired_pay';

    public function getDefaultDescription(): ?string
    {
        return match ($this) {
            self::UNIRED_PAY => 'PINFL orqali pul yechish uchun UNIRED',
            self::ATMOS_PINFL_PAY => 'PINFL orqali pul yechish uchun ATMOS',
            self::ATMOS_CARD_PAY => 'Karta orqali pul yechish uchun ATMOS',
            self::PROFILE_EDIT => 'Admin o\'z profilini o\'zgartira olish funksiyasi',
            self::BALANCE_FUND => 'Balansni to\'ldirish funksionali, agarda balansni to\'ldirishda muammo bo\'lsa ushbu feature o\'chirib yoqiladi',
            self::BALANCE_CHARGE => 'Balansdan pul yechish funksionali, agarda balansdan pul yechishda biron muammo bo\'lsa, ushbu feature ni o\'chirish orqali barcha pullik xizmatlarni cheklab qo\'yish mumkin',
            self::USER_MANAGEMENT => 'Foydalanuvchilarni boshqarish imkoniyati',
            self::ROLE_MANAGEMENT => 'Rollarni boshqarish imkoniyati',
            self::THEME_MANAGEMENT => 'Foydalanuvchi UI ni o\'ziga moslashtira olishi imkoniyati',
            self::ACTIVITY_LOG => 'Foydalanuvchi loglarini ko\'rish imkoniyati',
            self::SCORING_CREDIT_HISTORY => 'Kredit tarixini KATM orqali olish',
            self::SCORING_PENSION_INFO => 'Pensiya tarixini KATM orqali olish',
            self::SCORING_INPS_HISTORY => 'Oyliklar tarixini KATM orqali olish',
            self::SCORING_UZCARD => 'UzCard kartalari yillik oborot skoringi',
            self::SCORING_HUMO => 'Humo kartalari yillik oborot skoringi',
            self::SCORING_TAX_INFO => 'Soliq qarzdorligi skoringi',
            self::SCORING_MIB_INFO => 'MIB qarzdorligi skoringi',
            self::BRANCH_MANAGEMENT => 'Filiallar boshqaruvi',
            self::COMPANY_MANAGEMENT => 'Kompaniya boshqaruvi',
            self::TRANSACTION_TYPE_MANAGEMENT => 'Tranzaksiya turlari boshqaruvi',
            self::TRANSACTION_MANAGEMENT => 'Tranzaksiyalar boshqaruvi',
            self::TARIFF_MANAGEMENT => 'Tariflar boshqaruvi',
            self::CUSTOMER_MANAGEMENT => 'Mijozlar bazasi boshqaruvi',
            self::CUSTOMER_CARDS_MANAGEMENT => 'Mijozlar kartalari boshqaruvi',
            self::CALCULATOR_INSTALLMENT => 'Muddatli to\'lov kalkulyatori',
            self::INSTALLMENT_MANAGEMENT => 'Muddatli to\'lovlar boshqaruvi',
            self::INSTALLMENT_GUARANTOR => 'Muddatli to\'lovlarga kafil qo\'shish imkoniyati',
            self::INSTALLMENT_COMMENT => 'Muddatli to\'lovlarga izoh qo\'shish imkoniyati',
            self::INSTALLMENT_DOCUMENT => 'Muddatli to\'lovlarga xujjat qo\'shish imkoniyati',
            self::SMS => 'Sms yuborish xizmati',
            self::INSTALLMENT_AUTO_CHARGE => 'Muddatli to\'lovda kartadan avtomatik to\'lov',
            self::INSTALLMENT_AUTO_CHARGE_BY_PINFL => 'Muddatli to\'lovda PINFL orqali avtomatik to\'lov',
        };
    }

    public function getExampleData(): ?string
    {
        return match ($this) {
            self::ATMOS_PINFL_PAY,
            self::ATMOS_CARD_PAY => '{"username": "", "password": "", "store_id": 0, "expires_in": 0, "access_token": "", "token_type": "Bearer"}',
            self::UNIRED_PAY => '{"name" : "", "merchant_uzcard" : "", "terminal_uzcard" : "", "merchant_humo" : "", "terminal_humo" : "", "phone" : ""}',
            self::SCORING_CREDIT_HISTORY,
            self::SCORING_PENSION_INFO,
            self::SCORING_INPS_HISTORY,
            self::SCORING_UZCARD,
            self::SCORING_HUMO,
            self::SCORING_TAX_INFO,
            self::SCORING_MIB_INFO => '{"provider": "asaxiy", "username": "username", "password": "password"}',
            self::SMS => '{"provider": "sms_fly", "username": "username", "secret": "secret"}',
            default => null,
        };
    }

    public function isChargeable(): ?bool
    {
        return in_array($this, [
            self::SCORING_CREDIT_HISTORY,
            self::SCORING_PENSION_INFO,
            self::SCORING_INPS_HISTORY,
            self::SCORING_UZCARD,
            self::SCORING_HUMO,
            self::SCORING_TAX_INFO,
            self::SCORING_MIB_INFO,
            self::SMS,
            self::INSTALLMENT_AUTO_CHARGE,
            self::INSTALLMENT_AUTO_CHARGE_BY_PINFL,
        ]);
    }
}
