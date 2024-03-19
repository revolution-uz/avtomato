<?php

namespace App\Services;

use App\Http\Requests\Customer\CreateCustomerRequest;
use App\Http\Requests\Customer\UpdateCustomerRequest;
use App\Models\Company\Customer;
use Carbon\Carbon;
use Throwable;

class CustomerService
{
    /**
     * @throws Throwable
     */
    public function saveCustomer(CreateCustomerRequest|UpdateCustomerRequest $request, $customerId = null): Customer
    {
        if ($customerId) {
            $customer = Customer::findOrFail($customerId);
        } else {
            $customer = new Customer;
        }

        $customer->pinfl = $request->pinfl;
        $customer->tin = $request->tin;
        $customer->passport_series = $request->passport_series;
        $customer->passport_number = $request->passport_number;
        $customer->passport_given_date = Carbon::parse($request->passport_given_date);
        $customer->passport_expiration_date = Carbon::parse($request->passport_expiration_date);
        $customer->passport_given_place = $request->passport_given_place;
        $customer->passport_citizenship = $request->passport_citizenship;
        $customer->first_name = $request->first_name;
        $customer->last_name = $request->last_name;
        $customer->middle_name = $request->middle_name;
        $customer->birth_date = Carbon::parse($request->birth_date);
        $customer->birth_place = $request->birth_place;
        $customer->gender = $request->gender;
        $customer->saveOrFail();

        return $customer;
    }
}
