<?php

namespace Database\Factories\Company;

use App\Models\Company\Feature;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class BalanceChargeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'amount' => $this->faker->numberBetween(1, 100),
            'chargeable_id' => Feature::query()->inRandomOrder()->first()->id,
            'chargeable_type' => Feature::class,
            'created_at' => Carbon::now(),
        ];
    }
}
