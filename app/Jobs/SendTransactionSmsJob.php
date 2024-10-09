<?php

namespace App\Jobs;

use App\Enums\Company\CompanySettingsEnum;
use App\Enums\Company\SmsMessageCustomerTypeEnum;
use App\Enums\Company\SmsMessageStatusEnum;
use App\Enums\FeatureEnum;
use App\Enums\SmsTemplateEnum;
use App\Models\Company\CompanySettings;
use App\Models\Company\Customer;
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

    private Transaction $_transaction;
    private Feature $_feature;
    private SmsTemplate $_template;
    private BalanceService $_balanceService;
    private SmsService $_smsService;

    public function __construct(private readonly int $transactionId) {}

    /**
     * @throws Exception|Throwable
     */
    public function handle(SmsService $smsService, BalanceService $balanceService): void
    {
        $this->_balanceService = $balanceService;
        $this->_smsService = $smsService;

        $this->_feature = Feature::findByName(FeatureEnum::SMS);
        if (! $this->_feature->is_active or ! $balanceService->isBalanceEnough()) {
            return;
        }

        $this->_transaction = Transaction::query()->findOrFail($this->transactionId);
        $isAutoPay = TransactionType::isAutoPay($this->_transaction->transaction_type_id);

        $setting = CompanySettings::getValue(
            $isAutoPay ? CompanySettingsEnum::SMS_INSTALLMENT_AUTO_CHARGE : CompanySettingsEnum::SMS_INSTALLMENT_NOTIFICATION
        );
        if ($setting == 0) {
            return;
        }

        $this->_template = SmsTemplate::query()->findOrFail($isAutoPay ? SmsTemplateEnum::AUTO_CHARGE->value : SmsTemplateEnum::CHARGE->value);

        $this->sendSmsToCustomerPhones($this->_transaction->customer, SmsMessageCustomerTypeEnum::CUSTOMER);
        foreach ($this->_transaction->installment->guarantors as $guarantor) {
            $this->sendSmsToCustomerPhones($guarantor, SmsMessageCustomerTypeEnum::GUARANTOR);
        }
    }

    public function tags(): array
    {
        return [
            'tenant:' . tenant('id'),
        ];
    }

    private function sendSmsToCustomerPhones(Customer $customer, SmsMessageCustomerTypeEnum $customerType): void
    {
        foreach ($customer->phones as $phone) {
            if (! $phone->pivot->installment_notifications) {
                continue;
            }
            $message = new SmsMessage;
            $message->phone = $phone->phone;
            $message->installment_id = $this->_transaction->installment_id;
            $message->customer_id = $customer->id;
            $message->customer_type = $customerType;
            $message->message = $this->_template->getMessage([
                'pan' => $this->_transaction->card_pan,
                'amount' => $this->_transaction->amount,
                'credit_number' => $this->_transaction->installment_id,
                'company' => tenant('name'),
                'phone' => tenant('owner_phone'),
            ]);
            $message->status = SmsMessageStatusEnum::ACCEPTED;
            $message->saveOrFail();
            $this->_balanceService->chargeAmount($this->_feature->name, $message);
            $this->_smsService->sendSms($this->_feature, $message);
        }
    }
}
