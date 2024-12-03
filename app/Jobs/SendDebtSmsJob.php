<?php

namespace App\Jobs;

use App\Enums\Company\CompanySettingsEnum;
use App\Enums\Company\SmsMessageStatusEnum;
use App\Enums\FeatureEnum;
use App\Models\Company\CompanySettings;
use App\Models\Company\Feature;
use App\Models\Company\Installment;
use App\Models\Company\SmsMessage;
use App\Models\Company\SmsTemplate;
use App\Services\SmsService;
use App\Services\User\BalanceService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendDebtSmsJob implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public readonly int $installmentId)
    {
        $this->onQueue('sms');
    }

    public function handle(SmsService $smsService, BalanceService $balanceService): void
    {
        $feature = Feature::findByName(FeatureEnum::SMS);
        $installment = Installment::query()->findOrFail($this->installmentId);

        $template = SmsTemplate::debtTemplates()
            ->whereJsonContains('data->overdue_days', $installment->overdue_days)
            ->first();

        if (
            CompanySettings::getValue(CompanySettingsEnum::SMS_INSTALLMENT_PAYMENT) == 0
            or ! $feature->is_active
            or ! $balanceService->isAvailableCompany()
            or ! $template
        ) {
            return;
        }

        foreach ($installment->customer->phones as $phone) {
            if (! $phone->pivot->installment_notifications) {
                continue;
            }
            $message = new SmsMessage;
            $message->phone = $phone->phone;
            $message->message = $template->getMessage([
                'credit_number' => $installment->id,
                'amount' => $installment->debtAmount,
                'company' => tenant('name'),
                'phone' => tenant('owner_phone'),
            ]);
            $message->status = SmsMessageStatusEnum::ACCEPTED;
            $message->saveOrFail();
            $balanceService->chargeAmount($feature->name, $message);
            $smsService->sendSms($feature, $message);
        }
    }

    /**
     * Get the unique ID for the job.
     */
    public function uniqueId(): string
    {
        return 'send-debt-sms-' . tenant('id') . '-' . $this->installmentId;
    }

    public function tags(): array
    {
        return [
            'tenant:' . tenant('id'),
        ];
    }
}
