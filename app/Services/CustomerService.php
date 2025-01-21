<?php

namespace App\Services;

use App\Enums\Company\CustomerTypeEnum;
use App\Models\Company\Customer;
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
        $customer->passport_given_date = $data['passport_given_date'];
        $customer->passport_expiration_date = $data['passport_expiration_date'];
        $customer->birth_date = $data['birth_date'];

        $customer->saveOrFail();

        return $customer;
    }
}
