<?php

namespace Database\Factories\Company;

use App\Models\Company\CardConfirmationSms;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class CardConfirmationSmsFactory extends Factory
{
    protected $model = CardConfirmationSms::class;

    public function definition(): array
    {
        return [
            'phone' => $this->faker->e164PhoneNumber(),
            'code' => $this->faker->randomNumber(6),
        ];
    }

    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'created_at' => Carbon::now()->subMinutes(3),
        ]);
    }
}
