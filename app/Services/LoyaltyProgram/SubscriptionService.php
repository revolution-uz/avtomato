<?php

namespace App\Services\LoyaltyProgram;

use App\Clients\AtmosCardClient;
use App\Enums\Company\PaymentStatusEnum;
use App\Exceptions\GeneralException;
use App\Models\Company\LoyaltyProgram\LoyaltyProgramPayment;
use App\Models\Company\LoyaltyProgram\Subscription;
use App\Models\Company\LoyaltyProgram\Tariff;
use App\Models\Company\LoyaltyProgram\User;
use GraphQL\Error\Error;
use Illuminate\Support\Carbon;

class SubscriptionService
{
    public function __construct(private AtmosCardClient $_atmosCardClient) {}

    public function createSubscription(User $user, array $args): Subscription
    {
        $tariff = Tariff::query()->findOrFail($args['tariff_id']);

        if ($user->subscriptions()->count() == 0 || $tariff->is_free_for_previous_subscribers) {
            $expired_at = Carbon::now()->addMonths($tariff->free_months);

        } else {
            try {
                $payment = $this->payForSubscription($user, $tariff);
                $this->payForSubscriptionConfirm($payment);

            } catch (GeneralException $e) {
                throw new Error($e->getMessage());
            }

            $expired_at = Carbon::now()->addMonths($tariff->subscription_months);
        }

        $subscription = Subscription::query()->create([
            'user_id' => $user->id,
            'tariff_id' => $tariff->id,
            'remaining_limit_amount' => $tariff->limit_amount,
            'expired_at' => $expired_at,
        ]);

        return $subscription->refresh();
    }

    public function chargeSubscription(Subscription $subscription)
    {
        try {
            $payment = $this->payForSubscription($subscription->user, $subscription->tariff);
            $this->payForSubscriptionConfirm($payment);
        } catch (GeneralException $e) {
        }

        $subscription->remaining_limit_amount = $subscription->tariff->limit_amount;
        $subscription->expired_at = Carbon::now()->addMonths($subscription->tariff->subscription_months);
        $subscription->saveOrFail();

        return $subscription;
    }

    public function payForSubscription(User $user, Tariff $tariff): LoyaltyProgramPayment
    {
        $createPayResponseDTO = $this->_atmosCardClient->createPayment(
            $tariff->id,
            $tariff->amount,
            'Payment for Loyalty program tariff #' . $tariff->id
        );

        if (! $createPayResponseDTO->result->isSuccess()) {
            throw new GeneralException($createPayResponseDTO->result->description);
        }

        $card = null;
        foreach ($user->cards as $index => $card) {
            $preConfirmPayResponseDTO = $this->_atmosCardClient->preConfirmPayment($card->external_id, $createPayResponseDTO->transaction_id);

            if ($preConfirmPayResponseDTO->result->isSuccess()) {
                break;
            }

            if ($index === $user->cards->count() - 1) {
                throw new GeneralException($preConfirmPayResponseDTO->result->description);
            }
        }

        return LoyaltyProgramPayment::query()->create([
            'amount' => $createPayResponseDTO->store_transaction->amount / 100,
            'card_id' => $card->id,
            'status' => PaymentStatusEnum::WAITING_FOR_CONFIRMATION,
            'external_id' => $createPayResponseDTO->transaction_id,
            'order_id' => $tariff->id,
            'user_id' => $user->id,
        ]);
    }

    public function payForSubscriptionConfirm(LoyaltyProgramPayment $payment): true
    {
        $confirmPayResponseDTO = $this->_atmosCardClient->confirmPayment($payment->external_id);

        if (! $confirmPayResponseDTO->result->isSuccess()) {
            throw new GeneralException($confirmPayResponseDTO->result->description);
        }

        $payment->status = PaymentStatusEnum::SUCCESS;
        $payment->paid_at = Carbon::createFromTimestampMs($confirmPayResponseDTO->store_transaction->confirm_time);

        if (! $payment->save()) {
            $this->_atmosCardClient->reversePayment($payment->external_id);
        }

        return true;
    }
}
