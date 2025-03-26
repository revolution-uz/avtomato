<?php

namespace Database\Factories\Company\LoyaltyProgram;

use App\Enums\Company\CardStatusEnum;
use App\Models\Company\LoyaltyProgram\Card;
use App\Models\Company\LoyaltyProgram\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class CardFactory extends Factory
{
    protected $model = Card::class;

    public function definition(): array
    {
        $cardNumber = $this->faker->regexify('[1-9]{16}');

        return [
            'pan' => substr_replace($cardNumber, '******', 6, 6),
            'number' => $cardNumber,
            'hash' => $cardNumber,
            'expiry' => $this->faker->date('ym'),
            'phone' => $this->faker->e164PhoneNumber(),
            'status' => CardStatusEnum::randomValue(),
            'balance' => $this->faker->randomFloat(2),
            'external_id' => $this->faker->uuid(),
            'owner' => $this->faker->name(),

            'user_id' => User::factory(),
        ];
    }

    public function deleted(): static
    {
        return $this->state(fn (array $attributes) => [
            'deleted_at' => Carbon::now(),
        ]);
    }
}
