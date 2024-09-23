<?php

namespace App\Jobs;

use App\Enums\Company\CompanySettingsEnum;
use App\Enums\Company\SmsMessageStatusEnum;
use App\Enums\FeatureEnum;
use App\Enums\SmsTemplateEnum;
use App\Models\Company\CompanySettings;
use App\Models\Company\Feature;
use App\Models\Company\SmsMessage;
use App\Models\Company\SmsTemplate;
use App\Models\Company\Transaction;
use App\Models\Company\TransactionType;
use App\Services\SmsService;
use App\Services\User\BalanceService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class SendTransactionSmsJob implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private readonly int $transactionId) {}

    /**
     * @throws Exception|Throwable
     */
    public function handle(SmsService $smsService, BalanceService $balanceService): void
    {
        $feature = Feature::findByName(FeatureEnum::SMS);
        if (! $feature->is_active or ! $balanceService->isBalanceEnough()) {
            return;
        }

        $transaction = Transaction::query()->findOrFail($this->transactionId);
        $isAutoPay = TransactionType::isAutoPay($transaction->transaction_type_id);

        $setting = CompanySettings::getValue(
            $isAutoPay ? CompanySettingsEnum::SMS_INSTALLMENT_AUTO_CHARGE : CompanySettingsEnum::SMS_INSTALLMENT_NOTIFICATION
        );
        if ($setting == 0) {
            return;
        }

        $template = SmsTemplate::query()->findOrFail($isAutoPay ? SmsTemplateEnum::AUTO_CHARGE->value : SmsTemplateEnum::CHARGE->value);

        $customers = clone $transaction->installment->guarantors;
        $customers->push($transaction->customer);
        foreach ($customers as $customer) {
            foreach ($customer->phones as $phone) {
                if (! $phone->pivot->installment_notifications) {
                    continue;
                }
                $message = new SmsMessage;
                $message->phone = $phone->phone;
                $message->message = $template->getMessage([
                    'pan' => $transaction->card_pan,
                    'amount' => $transaction->amount,
                    'credit_number' => $transaction->installment_id,
                    'company' => tenant('name'),
                    'phone' => tenant('owner_phone'),
                ]);
                $message->status = SmsMessageStatusEnum::ACCEPTED;
                $message->saveOrFail();
                $balanceService->chargeAmount($feature->name, $message);
                $smsService->sendSms($feature, $message);
            }
        }
    }

    public function tags(): array
    {
        return [
            'tenant:' . tenant('id'),
        ];
    }
}
