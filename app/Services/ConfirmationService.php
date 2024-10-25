<?php

namespace App\Services;

use App\Enums\Company\AllowedEnum;
use App\Models\Company\CardConfirmationSms;
use Carbon\Carbon;
use Exception;

class ConfirmationService
{
    public function __construct(
        private readonly SmsService $smsService
    ) {}

    /**
     * @throws Exception
     */
    public function send(string $phone, bool $isMarketOtp = false): true
    {
        /** @var CardConfirmationSms|null $authAttempt */
        $authAttempt = CardConfirmationSms::query()
            ->where('phone', $phone)
            ->where('created_at', '>', Carbon::now()->subMinutes(2))
            ->exists();

        if ($authAttempt) {
            return true;
        }

        $code = $this->generateCode();
        $confirmationSms = CardConfirmationSms::query()->create([
            'phone' => $phone,
            'code' => $code,
        ]);
        $this->smsService->sendConfirmationSms($confirmationSms, $isMarketOtp);

        return true;
    }

    public function confirm(string $phone, string $otp): bool
    {
        if ($phone == AllowedEnum::PHONE->value && $otp === AllowedEnum::OTP_CODE->value) {
            return true;
        }
        $authAttempt = CardConfirmationSms::query()
            ->where('phone', $phone)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($authAttempt && $authAttempt->code == $otp && $authAttempt->created_at->gt(Carbon::now()->subMinutes(2))) {
            $authAttempt->delete();

            return true;
        }

        return false;
    }

    /**
     * @throws Exception
     */
    private function generateCode(): string
    {
        $length = 6;
        do {
            $random = (string) rand(10 ** ($length - 1), 10 ** $length - 1);
        } while (str_contains($random, '0'));

        return $random;
    }
}
