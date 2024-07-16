<?php

namespace App\Services;

use App\Clients\DocumentGeneratorClient;
use App\Enums\Company\AddressTypeEnum;
use App\Models\Company\Address;
use App\Models\Company\Card;
use App\Models\Company\Customer;
use App\Models\Company\Installment;
use App\Models\Company\Phone;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

readonly class DocumentGeneratorService
{
    public function __construct(
        private DocumentGeneratorClient $documentGeneratorClient
    ) {}

    public function generateCustomerApplicationStream(Customer $customer): StreamedResponse
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

        return $this->documentGeneratorClient->generateStream($customer->full_name . ' ' . date('d-m-Y--H-i-s.docx'), 'customer-application.docx', $data);
    }

    public function generateInstallmentFormStream(Installment $installment): StreamedResponse
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
                'starts_in#' . $i => $graph->starts_in?->format('d.m.Y'),
                'sum#' . $i => number_format($graph->amount, 2, ' ', '.') . ' / ' . number_format($graph->paid_amount, 2, ' ', '.'),
            ]);
        }
        $data['paid_price'] = number_format($paid_sum, 2, ' ', '.');

        return $this->documentGeneratorClient->generateStream($customer->full_name . ' ' . date('d-m-Y--H-i-s.docx'), 'installment-form.docx', $data);
    }
}
