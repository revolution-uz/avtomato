<?php

namespace Database\Factories\Company;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class BalanceChargeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'amount' => $this->faker->numberBetween(1, 100),
            'chargeable_id' => $this->faker->numberBetween(1, 100),
            'chargeable_type' => $this->faker->word(),
            'created_at' => Carbon::now(),
        ];
    }
}
