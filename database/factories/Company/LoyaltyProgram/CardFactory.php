<?php

namespace Database\Factories\Company\LoyaltyProgram;

use App\Enums\Company\CardStatusEnum;
use App\Models\Company\LoyaltyProgram\Card;
use App\Models\Company\LoyaltyProgram\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CardFactory extends Factory
{
    protected $model = Card::class;

    public function definition(): array
    {
        return [
            'pan' => $this->faker->creditCardNumber,
            'number' => $this->faker->creditCardNumber,
            'hash' => $this->faker->randomNumber,
            'expiry' => $this->faker->creditCardExpirationDateString,
            'owner' => $this->faker->name,
            'phone' => $this->faker->e164PhoneNumber(),
            'status' => CardStatusEnum::randomValue(),
            'balance' => $this->faker->randomFloat(2, 0, 10000),
            'external_id' => $this->faker->uuid,

            'user_id' => User::factory(),
        ];
    }
}
