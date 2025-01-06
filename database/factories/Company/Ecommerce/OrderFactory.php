<?php

namespace Database\Factories\Company\Ecommerce;

use App\Models\Company\Address;
use App\Models\Company\Ecommerce\OrderStatus;
use App\Models\Company\Ecommerce\User;
use App\Models\Company\Installment;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'address_id' => Address::factory(),
            'status_id' => OrderStatus::factory(),
            'installment_id' => Installment::factory(),
        ];
    }
}
