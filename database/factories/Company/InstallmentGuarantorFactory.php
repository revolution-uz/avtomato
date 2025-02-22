<?php

namespace Database\Factories\Company;

use App\Models\Company\Customer;
use App\Models\Company\Installment;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class InstallmentGuarantorFactory extends Factory
{
    public function definition(): array
    {
        return [
            'unired_id' => $this->faker->randomNumber(),
            'any_card_charge' => $this->faker->boolean(),
            'plum_id' => Str::uuid(),

            'installment_id' => Installment::factory(),
            'customer_id' => Customer::factory(),
        ];
    }
}
