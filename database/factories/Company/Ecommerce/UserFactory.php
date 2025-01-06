<?php

namespace Database\Factories\Company\Ecommerce;

use App\Models\Company\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'phone' => $this->faker->phoneNumber,
            'customer_id' => Customer::factory(),
        ];
    }
}
