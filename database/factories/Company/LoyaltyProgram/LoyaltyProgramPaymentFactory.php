<?php

namespace Database\Factories\Company\LoyaltyProgram;

use App\Enums\Company\PaymentStatusEnum;
use App\Models\Company\LoyaltyProgram\Card;
use App\Models\Company\LoyaltyProgram\LoyaltyProgramPayment;
use App\Models\Company\LoyaltyProgram\Subscription;
use App\Models\Company\LoyaltyProgram\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LoyaltyProgramPaymentFactory extends Factory
{
    protected $model = LoyaltyProgramPayment::class;

    public function definition(): array
    {
        return [
            'amount' => $this->faker->randomFloat(2, 0, pow(10, 8)),
            'status' => PaymentStatusEnum::randomValue(),
            'paid_at' => $this->faker->boolean() ? $this->faker->dateTime() : null,
            'external_id' => $this->faker->boolean() ? $this->faker->uuid() : null,

            'card_id' => Card::factory(),
            'subscription_id' => Subscription::factory(),
            'user_id' => User::factory(),
        ];
    }
}
