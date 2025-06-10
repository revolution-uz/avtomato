<?php

namespace Database\Factories\Company\LoyaltyProgram;

use App\Models\Company\LoyaltyProgram\Subscription;
use App\Models\Company\LoyaltyProgram\Tariff;
use App\Models\Company\LoyaltyProgram\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubscriptionFactory extends Factory
{
    protected $model = Subscription::class;

    public function definition(): array
    {
        return [
            'is_active' => $this->faker->boolean,
            'remaining_limit_amount' => $this->faker->numberBetween(1000, pow(10, 8)),
            'expired_at' => $this->faker->dateTime(),

            'tariff_id' => Tariff::factory(),
            'user_id' => User::factory(),
        ];
    }

    public function active(): static
    {
        return $this->state([
            'is_active' => true,
            'expired_at' => now()->addDays($this->faker->numberBetween(1, 30)),

            'tariff_id' => Tariff::factory()->active()->create()->id,
        ]);
    }
}
