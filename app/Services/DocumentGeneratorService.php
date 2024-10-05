<?php

namespace App\Services;

use App\Clients\DocumentGeneratorClient;
use App\Enums\Company\AddressTypeEnum;
use App\Helpers\InstallmentGraphHelper;
use App\Models\Company;
use App\Models\Company\Address;
use App\Models\Company\Customer;
use App\Models\Company\Installment;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

readonly class DocumentGeneratorService
{
    public function __construct(
        private DocumentGeneratorClient $documentGeneratorClient
    ) {}

    public function generateCustomerApplicationStream(Installment $installment): StreamedResponse
    {
        /** @var Company $company */
        $company = tenant();

        $data = [
            'company_name' => $company->name,
            'company_bank' => $company->bank_name,
            'company_account' => $company->account_number,
            'company_tin' => $company->tin,
            'company_phone' => $company->owner_phone,
            'company_owner' => $company->owner_name,
            'company_bank_code' => $company->bank_code,
            'company_address' => $company->address,
            'installment_id' => $installment->id,
            'installment_created_at' => $this->formatDate($installment->created_at),
            'installment_period' => $installment->period,
            'installment_whole_amount' => $this->formatAmount($installment->wholeDebtAmount),
            'installment_description' => $installment->description,
            'customer_address' => $installment->customer->addresses()->first()->address,
            'customer_full_name' => $installment->customer->full_name,
            'customer_passport' => $installment->customer->passport,
            'customer_tin' => $installment->customer->tin,
            'customer_phone' => $installment->customer->phones()->first()->phone,
        ];

        $i = 0;
        foreach (InstallmentGraphHelper::getGraphs($installment) as $graph) {
            $i++;
            $data = array_merge($data, [
                "i#{$i}" => $i,
                "starts_in#{$i}" => $graph->starts_in->format('d.m.Y'),
                "sum#{$i}" => $this->formatAmount($graph->amount),
            ]);
        }

        $table = ['identifier' => 'i', 'row_count' => $i];

        return $this->documentGeneratorClient->generateStream(
            $this->generateFileName($installment->customer->full_name),
            'customer-application.docx',
            $data,
            [$table]
        );
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
        foreach (InstallmentGraphHelper::getGraphs($installment) as $graph) {
            $i++;
            $paid_sum += $graph->paid_amount;
            $data = array_merge($data, [
                'i#' . $i => $i,
                'starts_in#' . $i => $graph->starts_in->format('d.m.Y'),
                'sum#' . $i => number_format($graph->amount, 2, ' ', '.') . ' / ' . number_format($graph->paid_amount, 2, ' ', '.'),
            ]);
        }
        $data['paid_price'] = number_format($paid_sum, 2, ' ', '.');

        return $this->documentGeneratorClient->generateStream($customer->full_name . ' ' . date('d-m-Y--H-i-s.docx'), 'installment-form.docx', $data);
    }

    public function generateGuarantorApplicationStream(Installment $installment, Customer $guarantor): StreamedResponse
    {
        /** @var Company $company */
        $company = tenant();

        $data = [
            'company_name' => $company->name,
            'company_bank' => $company->bank_name,
            'company_account' => $company->account_number,
            'company_tin' => $company->tin,
            'company_phone' => $company->owner_phone,
            'company_owner' => $company->owner_name,
            'installment_id' => $installment->id,
            'installment_created_at' => $this->formatDate($installment->created_at),
            'installment_amount' => $this->formatAmount($installment->amount),
            'installment_period' => $installment->period,
            'guarantor_address' => $guarantor->addresses()->first()->address,
            'guarantor_full_name' => $guarantor->full_name,
            'guarantor_passport' => $guarantor->passport,
            'guarantor_tin' => $guarantor->tin,
            'guarantor_phone' => $guarantor->phones()->first()->phone,
        ];

        return $this->documentGeneratorClient->generateStream(
            $this->generateFileName($guarantor->full_name),
            'guarantor-application.docx',
            $data
        );
    }

    private function formatDate(Carbon $date): string
    {
        $months = ['Yanvar', 'Fevral', 'Mart', 'Aprel', 'May', 'Iyun', 'Iyul', 'Avgust', 'Sentyabr', 'Oktyabr', 'Noyabr', 'Dekabr'];

        return "«{$date->day}» {$months[$date->month - 1]} {$date->year} yil";
    }

    private function generateFileName($fullName)
    {
        $sanitizedFileName = preg_replace("/[\/\?%*:|\"<>\s']+/", '-', Str::kebab($fullName));
        $currentDate = Carbon::now()->format('d-m-Y-H-i-s');

        return "{$sanitizedFileName}-{$currentDate}.docx";
    }

    private function formatAmount($amount): string
    {
        return number_format($amount, 2, '.', ' ');
    }
}
