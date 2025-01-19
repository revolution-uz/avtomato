<?php

namespace App\Enums\Company;

enum AtmosPinflPaymentCode: int
{
    case SUCCESS = 200;
    case UNEXPECTED_EXCEPTION = -100;
    case CONTRACT_REGISTRATION_FAILED = -101;
    case CLIENT_REGISTRATION_FAILED = -102;
    case CLIENT_NOT_AVAILABLE = -103;
    case NO_CARDS_WITH_BALANCE = -105;
    case INSUFFICIENT_BALANCE = -106;
    case PAYMENT_NOT_INITIATED = -107;
    case CARDS_BALANCE_UNAVAILABLE = -108;
    case CARDS_NOT_FOUND = -109;
    case GENERIC_ERROR = -200;
    case TRANSACTION_FAILED = -202;
    case DATA_NOT_FOUND = -301;
    case EXTERNAL_ID_USED = -302;
    case INVALID_INPUT = -303;
    case AMOUNT_TOO_SMALL = -306;
    case EPOS_NOT_FOUND = -307;

    public static function getName(int $type): string
    {
        return match ($type) {
            self::SUCCESS->value => __('api.' . self::SUCCESS->name),
            self::UNEXPECTED_EXCEPTION->value => __('api.' . self::UNEXPECTED_EXCEPTION->name),
            self::CONTRACT_REGISTRATION_FAILED->value => __('api.' . self::CONTRACT_REGISTRATION_FAILED->name),
            self::CLIENT_REGISTRATION_FAILED->value => __('api.' . self::CLIENT_REGISTRATION_FAILED->name),
            self::CLIENT_NOT_AVAILABLE->value => __('api.' . self::CLIENT_NOT_AVAILABLE->name),
            self::NO_CARDS_WITH_BALANCE->value => __('api.' . self::NO_CARDS_WITH_BALANCE->name),
            self::INSUFFICIENT_BALANCE->value => __('api.' . self::INSUFFICIENT_BALANCE->name),
            self::PAYMENT_NOT_INITIATED->value => __('api.' . self::PAYMENT_NOT_INITIATED->name),
            self::CARDS_BALANCE_UNAVAILABLE->value => __('api.' . self::CARDS_BALANCE_UNAVAILABLE->name),
            self::CARDS_NOT_FOUND->value => __('api.' . self::CARDS_NOT_FOUND->name),
            self::GENERIC_ERROR->value => __('api.' . self::GENERIC_ERROR->name),
            self::TRANSACTION_FAILED->value => __('api.' . self::TRANSACTION_FAILED->name),
            self::DATA_NOT_FOUND->value => __('api.' . self::DATA_NOT_FOUND->name),
            self::EXTERNAL_ID_USED->value => __('api.' . self::EXTERNAL_ID_USED->name),
            self::INVALID_INPUT->value => __('api.' . self::INVALID_INPUT->name),
            self::AMOUNT_TOO_SMALL->value => __('api.' . self::AMOUNT_TOO_SMALL->name),
            self::EPOS_NOT_FOUND->value => __('api.' . self::EPOS_NOT_FOUND->name),
            default => 'Not found'
        };
    }

    public static function getDetails(int $type, ?int $additional_info = null): string
    {
        if ($type == self::TRANSACTION_FAILED->value) {
            return $additional_info ? __('api.TRANSACTION_FAILED :transaction_id', ['transaction_id' => $additional_info]) : __('api.TRANSACTION_FAILED');
        }
        if ($type == self::CARDS_NOT_FOUND->value) {
            return $additional_info ? __('api.CARDS_NOT_FOUND :additional_info', ['additional_info' => $additional_info]) : __('api.CARDS_NOT_FOUND');
        }

        return self::getName($type);
    }
}
