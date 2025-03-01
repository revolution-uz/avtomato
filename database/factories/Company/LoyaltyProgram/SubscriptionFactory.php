<?php

namespace Database\Factories\Company\LoyaltyProgram;

use App\Models\Company\LoyaltyProgram\Card;
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
            'card_id' => Card::factory(),
            'tariff_id' => Tariff::factory(),
            'user_id' => User::factory(),
        ];
    }
}
