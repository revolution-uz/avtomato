<?php

namespace App\Services;

use App\Enums\Company\LoyaltyProgramm\CanLoginTypeEnum;
use App\Helpers\Utilities;
use App\Models\Company\CardConfirmationSms;
use App\Models\Company\LoyaltyProgram\User;
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

    public function canLogin(User $user, string $deviceName, int $attempts): CanLoginTypeEnum
    {
        if ($success_login_attempts = $user->getSuccessLoginAttempts($deviceName, Utilities::A_MONTH)) {
            return $success_login_attempts < $attempts ? CanLoginTypeEnum::LAST_CHANCE : CanLoginTypeEnum::CANNOT_LOGIN;
        }

        return CanLoginTypeEnum::CAN_LOGIN;
    }

    public function canLoginIntoLoyaltyProgramm(string $phone, string $deviceName): CanLoginTypeEnum
    {
        if ($user = User::query()->where('phone', $phone)->first()) {
            return $this->canLogin($user, $deviceName, Utilities::LOYALTY_USER_MAX_SESSIONS);
        }

        return CanLoginTypeEnum::CAN_LOGIN;
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
