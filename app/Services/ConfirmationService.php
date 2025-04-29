<?php

namespace App\Services;

use App\Enums\Company\LoyaltyProgram\CanLoginTypeEnum;
use App\Enums\Company\SmsTemplateEnum;
use App\Helpers\Utilities;
use App\Models\Company\CardConfirmationSms;
use App\Models\Company\LoyaltyProgram\User;
use Exception;
use Illuminate\Support\Carbon;

class ConfirmationService
{
    private const SMS_RESEND_INTERVAL = 2;

    public function __construct(private readonly SmsService $smsService) {}

    /**
     * @throws Exception
     */
    public function send(string $phone, SmsTemplateEnum $smsTemplateEnum): true
    {
        if ($this->canSendSms($phone)) {
            $confirmationSms = CardConfirmationSms::query()->create(['phone' => $phone, 'code' => $this->generateCode()]);
            $this->smsService->sendConfirmationSms($confirmationSms, $smsTemplateEnum);
        }

        return true;
    }

    public function confirm(string $phone, string $otp): bool
    {
        if (
            $authAttempt = CardConfirmationSms::query()
                ->where('created_at', '>=', Carbon::now()->subMinutes(self::SMS_RESEND_INTERVAL))
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
        if (
            $deviceName !== $user->main_device &&
            $success_login_attempts = $user->getSuccessLoginAttempts(Utilities::A_MONTH)
        ) {
            return $success_login_attempts < $attempts ? CanLoginTypeEnum::LAST_CHANCE : CanLoginTypeEnum::CANNOT_LOGIN;
        }

        return CanLoginTypeEnum::CAN_LOGIN;
    }

    public function canLoginIntoLoyaltyProgram(string $phone, string $deviceName): CanLoginTypeEnum
    {
        if ($user = User::query()->where('phone', $phone)->first()) {
            return $this->canLogin($user, $deviceName, Utilities::LOYALTY_USER_MAX_SESSIONS);
        }

        return CanLoginTypeEnum::CAN_LOGIN;
    }

    private function canSendSms(string $phone): bool
    {
        return CardConfirmationSms::query()
            ->where('phone', $phone)
            ->where('created_at', '>', Carbon::now()->subMinutes(self::SMS_RESEND_INTERVAL))
            ->doesntExist();
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
