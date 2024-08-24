<?php

namespace App\Jobs;

use App\Enums\Company\SmsMessageStatusEnum;
use App\Enums\FeatureEnum;
use App\Enums\SmsTemplateEnum;
use App\Models\Company;
use App\Models\Company\Feature;
use App\Models\Company\SmsMessage;
use App\Models\Company\SmsTemplate;
use App\Models\Company\Transaction;
use App\Services\SmsService;
use App\Services\User\BalanceService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendTransactionSmsJob implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(private readonly int $transactionId) {}

    /**
     * @throws Exception
     */
    public function handle(SmsService $smsService, BalanceService $balanceService): void
    {
        $feature = Feature::findByName(FeatureEnum::SMS);
        $template = SmsTemplate::query()->findOrFail(SmsTemplateEnum::TEMPLATE_AUTO_CHARGE);

        if (! $feature->is_active) {
            return;
        }

        /** @var Company $company */
        $company = tenancy()->tenant;
        $transaction = Transaction::findOrFail($this->transactionId);

        $customers = clone $transaction->installment->guarantors;
        $customers->push($transaction->customer);
        foreach ($customers as $customer) {
            foreach ($customer->phones as $phone) {
                if ($phone->pivot->installment_notifications) {
                    $message = new SmsMessage;
                    $message->phone = $phone->phone;
                    $message->message = $template->getMessage([
                        'pan' => $transaction->card_pan,
                        'amount' => $transaction->amount,
                        'credit_number' => $transaction->installment_id,
                        'company' => $company->name,
                        'phone' => $company->owner_phone,
                    ]);
                    $message->status = SmsMessageStatusEnum::ACCEPTED;
                    $message->saveOrFail();
                    $balanceService->chargeAmount($feature->name, $message);
                    $smsService->sendSms($feature, $message);
                }
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
