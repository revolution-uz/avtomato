<?php

namespace App\Services;

use App\Enums\Company\CustomerTypeEnum;
use App\Models\Company\Customer;
use Carbon\Carbon;
use Throwable;

class CustomerService
{
    /**
     * @throws Throwable
     */
    public function saveCustomer(?array $data, $customerId = null, CustomerTypeEnum $type = CustomerTypeEnum::INSTALLMENT): Customer
    {
        if ($customerId) {
            $customer = Customer::findOrFail($customerId);
        } else {
            $customer = new Customer;
        }
        $customer->fill($data);
        if ($type === CustomerTypeEnum::ECOMMERCE) {
            $customer->saveOrFail();

            return $customer;
        }
        $customer->passport_given_date = Carbon::make($data['passport_given_date'])->format('Y-m-d');
        $customer->passport_expiration_date = Carbon::make($data['passport_expiration_date'])->format('Y-m-d');
        $customer->birth_date = Carbon::make($data['birth_date'])->format('Y-m-d');

        $customer->saveOrFail();

        return $customer;
    }
}
