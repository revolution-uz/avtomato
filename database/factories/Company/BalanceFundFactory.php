<?php

namespace Database\Factories\Company;

use Illuminate\Database\Eloquent\Factories\Factory;

class BalanceFundFactory extends Factory
{
    public function definition(): array
    {
        return [
            'amount' => rand(1, 100),
            'phone_number' => $this->faker->numberBetween(1, 100),
            'external_transaction_id' => $this->faker->numberBetween(1, 100),
        ];
    }
}
