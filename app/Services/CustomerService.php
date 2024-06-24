<?php

namespace App\Services;

use App\Models\Company\Customer;
use Throwable;

class CustomerService
{
    /**
     * @throws Throwable
     */
    public function saveCustomer(array $data, $customerId = null): Customer
    {
        if ($customerId) {
            $customer = Customer::findOrFail($customerId);
        } else {
            $customer = new Customer;
        }

        $customer->fill($data);
        $customer->saveOrFail();

        return $customer;
    }
}
