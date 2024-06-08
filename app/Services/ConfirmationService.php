<?php

namespace App\Services;

use App\Clients\SmsFlyClient;
use App\DTOs\Sms\SendMessageDTO;
use App\Models\Company\CardConfirmationSms;
use Carbon\Carbon;
use Exception;

class ConfirmationService
{
    /**
     * @throws Exception
     */
    public function send(string $phone): true
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
        CardConfirmationSms::query()->create([
            'phone' => $phone,
            'code' => $code,
        ]);

        // todo: message template
        (new SmsFlyClient)->send(new SendMessageDTO($phone, "Your code: $code"));

        return true;
    }

    public function confirm(string $phone, string $otp): bool
    {
        $authAttempt = CardConfirmationSms::query()
            ->where('phone', $phone)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($authAttempt && $authAttempt->code === $otp && $authAttempt->created_at->gt(Carbon::now()->subMinutes(2))) {
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
            $random = (string)rand(10 ** ($length - 1), 10 ** $length - 1);

        } while (str_contains($random, '0'));

        return $random;
    }
}
