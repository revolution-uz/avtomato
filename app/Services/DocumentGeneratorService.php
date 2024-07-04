<?php

namespace App\Services;

use App\Clients\DocumentGeneratorClient;
use App\Enums\Company\AddressTypeEnum;
use App\Enums\Company\AttachmentTypeEnum;
use App\Models\Company\Address;
use App\Models\Company\Attachment;
use App\Models\Company\Card;
use App\Models\Company\Customer;
use App\Models\Company\Installment;
use App\Models\Company\Phone;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class DocumentGeneratorService
{
    public function __construct(
        private readonly DocumentGeneratorClient $documentGeneratorClient
    ) {}

    public function findOrCreateInstallmentForm(Installment $installment)
    {
        if ($attachment = Attachment::query()->where('type', AttachmentTypeEnum::INSTALLMENT_FORM_DOCUMENT)->where('attachmentable_id', $installment->id)->first()) {
            return $attachment;
        }

        return $this->saveAttachment($installment, $this->generateInstallmentForm($installment), AttachmentTypeEnum::INSTALLMENT_FORM_DOCUMENT);
    }

    public function findOrCreateCustomerApplication(Customer $customer)
    {
        if ($attachment = Attachment::query()->where('type', AttachmentTypeEnum::CUSTOMER_APPLICATION_DOCUMENT)->where('attachmentable_id', $customer->id)->first()) {
            return $attachment;
        }

        return $this->saveAttachment($customer, $this->generateCustomerApplication($customer), AttachmentTypeEnum::CUSTOMER_APPLICATION_DOCUMENT);
    }

    private function generateCustomerApplication(Customer $customer)
    {
        $phones = [];
        foreach ($customer->phones as $phone) {
            /** @var Phone $phone */
            $phones[] = $phone->phone;
        }
        $work_address = new Address;
        foreach ($customer->addresses as $address) {
            /** @var Address $address */
            if ($address->type == AddressTypeEnum::WORK_PLACE) {
                $work_address = $address;
            }
        }
        $card = new Card;
        foreach ($customer->cards as $_card) {
            $card = $_card;
        }
        $data = [
            'application_number' => $customer->id,
            'date' => $customer->created_at->format('d.m.Y'),
            'full_name' => $customer->full_name,
            'phone' => $phones[0] ?? '',
            'extra_phone' => $phones[1] ?? '',
            'work_address' => $work_address->full_address,
            'card' => $card->pan,
        ];

        return $this->documentGeneratorClient->generate($customer->full_name . ' ' . date('d-m-Y--H-i-s.docx'), 'customer-application.docx', $data);
    }

    private function generateInstallmentForm(Installment $installment)
    {
        $customer = $installment->customer;
        $living_address = new Address;
        foreach ($customer->addresses as $address) {
            /** @var Address $address */
            if ($address->type == AddressTypeEnum::LIVING_PLACE) {
                $living_address = $address;
            }
        }
        $paid_sum = 0;
        $total_amount = $installment->amount + $installment->initial_fee;
        $data = [
            'number' => $installment->id,
            'date' => $installment->created_at->format('d.m.Y'),
            'time' => Carbon::now()->format('d.m.Y'),
            'address' => $living_address->full_address,
            'full_name' => $customer->full_name,
            'total_amount' => number_format($total_amount, 2, ' ', '.'),
            'market' => $installment->branch->name,
            'm_id' => $installment->id,
            'passport' => $customer->passport_series . $customer->passport_number,
            'details' => $installment->description,
            'total' => number_format($total_amount, 2, ' ', '.') . ' / ' . number_format($paid_sum, 2, ' ', '.'),
        ];
        $i = 0;
        foreach ($installment->graphs as $graph) {
            $i++;
            $paid_sum += $graph->paid_amount;
            $data = array_merge($data, [
                'i#' . $i => $i,
                'starts_in#' . $i => $graph->starts_in->format('d.m.Y'),
                'sum#' . $i => number_format($graph->amount, 2, ' ', '.') . ' / ' . number_format($graph->paid_amount, 2, ' ', '.'),
            ]);
        }
        $data[] = ['paid_price' => number_format($paid_sum, 2, ' ', '.')];

        return $this->documentGeneratorClient->generate($customer->full_name . ' ' . date('d-m-Y--H-i-s.docx'), 'installment-form.docx', $data);
    }

    private function saveAttachment(Model $model, string $name, AttachmentTypeEnum $type)
    {
        $attachment = new Attachment;
        $attachment->type = $type;
        $attachment->path = storage_path('app/public/documents/') . $name;
        $attachment->name = $name;
        $attachment->original_name = $name;
        $attachment->mime = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
        $attachment->attachmentable_type = $model::class;
        $attachment->attachmentable_id = $model->getKey();
        $attachment->saveOrFail();

        return $attachment;
    }
}
