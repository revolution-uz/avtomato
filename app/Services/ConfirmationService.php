<?php

namespace App\Services;

use App\Models\Company\CardConfirmationSms;
use Carbon\Carbon;
use Exception;

class ConfirmationService
{
    public function __construct(private readonly SmsService $smsService) {}

    /**
     * @throws Exception
     */
    public function send(string $phone, bool $isEcommerceOtp = false): true
    {
        if (
            CardConfirmationSms::query()
                ->where('phone', $phone)
                ->where('created_at', '>', Carbon::now()->subMinutes(2))
                ->doesntExist()
        ) {
            $code = $this->generateCode();
            $confirmationSms = CardConfirmationSms::query()->create([
                'phone' => $phone,
                'code' => $code,
            ]);
            $this->smsService->sendConfirmationSms($confirmationSms, $isEcommerceOtp);
        }

        return true;
    }

    public function confirm(string $phone, string $otp): bool
    {
        if (
            $authAttempt = CardConfirmationSms::query()
                ->where('created_at', '>=', Carbon::now()->subMinutes(2))
                ->where('phone', $phone)
                ->where('code', $otp)
                ->first()
        ) {
            return $authAttempt->delete();
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
