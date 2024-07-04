<?php

namespace Database\Factories\Company;

use App\Enums\Company\InstallmentStatusEnum;
use App\Models\Company\Installment;
use Illuminate\Database\Eloquent\Factories\Factory;

class InstallmentGraphFactory extends Factory
{
    public function definition(): array
    {
        return [
            'installment_id' => Installment::factory(),
            'amount' => $this->faker->randomFloat(2, 0, 1000),
            'paid_amount' => $this->faker->randomFloat(2, 0, 1000),
            'overdue_days' => $this->faker->numberBetween(0, 100),
            'status' => InstallmentStatusEnum::randomValue(),
            'starts_in' => $this->faker->dateTimeBetween('-1 year', '1 year'),
        ];
    }
}
