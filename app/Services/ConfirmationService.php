<?php

namespace App\Services;

use App\Clients\SmsFlyClient;
use App\DTOs\Sms\SendMessageDTO;
use App\Models\Company\AuthAttempt;
use Carbon\Carbon;
use Exception;

class ConfirmationService
{
    /**
     * @throws Exception
     */
    public function send(string $phone): true
    {
        if (in_array($phone, config('test_users.allowed_phones'))) {
            return true;
        }

        /** @var AuthAttempt|null $authAttempt */
        $authAttempt = AuthAttempt::query()
            ->where('phone', $phone)
            ->where('updated_at', '>', Carbon::now()->subMinutes(5))
            ->exists();

        if ($authAttempt) {
            return true;
        }

        // todo: real code generation and sending to the user after integration with the SMS provider
        $code = 123456;
        $this->generateCode();
        AuthAttempt::query()->updateOrCreate(['phone' => $phone], ['code' => $code]);

        // todo: message template
        //        (new SmsFlyClient)->send(new SendMessageDTO($phone, "Your code: $code"));

        return true;
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
