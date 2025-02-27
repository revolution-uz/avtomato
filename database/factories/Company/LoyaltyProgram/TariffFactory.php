<?php

namespace Database\Factories\Company\LoyaltyProgram;

use App\Models\Company\LoyaltyProgram\Tariff;
use Illuminate\Database\Eloquent\Factories\Factory;

class TariffFactory extends Factory
{
    protected $model = Tariff::class;

    public function definition()
    {
        return [
            'name_uz' => $this->faker->word,
            'name_ru' => $this->faker->word,
            'amount' => $this->faker->numberBetween(1000, 100000),
            'free_months' => $this->faker->numberBetween(0, 12),
            'subscription_months' => $this->faker->numberBetween(1, 24),
            'is_free_for_previous_subscribers' => $this->faker->boolean,
            'limit_amount' => $this->faker->numberBetween(1000, 1000000),
            'is_active' => $this->faker->boolean,
        ];
    }
}
