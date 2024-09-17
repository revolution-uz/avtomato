<?php

namespace Database\Factories\Company;

use App\Models\Company\CardConfirmationSms;
use Illuminate\Database\Eloquent\Factories\Factory;

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
}
