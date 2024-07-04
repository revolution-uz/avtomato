<?php

namespace App\Services;

use App\Clients\DocumentGeneratorClient;
use App\Enums\Company\AddressTypeEnum;
use App\Models\Company\Address;
use App\Models\Company\Card;
use App\Models\Company\Customer;
use App\Models\Company\Phone;

class DocumentGeneratorService
{
    public function __construct(
        private readonly DocumentGeneratorClient $documentGeneratorClient
    ) {}

    public function generateCustomerApplication(Customer $customer)
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
            'work_address' => $work_address->address,
            'card' => $card->pan,
        ];

        return $this->documentGeneratorClient->generate($customer->full_name . ' ' . date('d-m-Y--H-i-s.docx'), 'customer-application.docx', $data);
    }
}
