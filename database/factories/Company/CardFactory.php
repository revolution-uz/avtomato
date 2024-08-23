<?php

namespace Database\Factories\Company;

use App\Enums\Company\CardStatusEnum;
use App\Models\Company\Card;
use App\Models\Company\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class CardFactory extends Factory
{
    protected $model = Card::class;

    public function definition(): array
    {
        $cardNumber = $this->faker->regexify('[1-9]{16}');

        return [
            'customer_id' => Customer::factory(),
            'pan' => substr_replace($cardNumber, '******', 6, 6),
            'number' => $cardNumber,
            'hash' => $cardNumber,
            'expiry' => $this->faker->regexify('[1-9]{4}'),
            'phone' => $this->faker->phoneNumber(),
            'status' => CardStatusEnum::randomValue(),
            'balance' => $this->faker->randomFloat(),
            'external_id' => $this->faker->word(),
            'owner' => $this->faker->firstName() . ' ' . $this->faker->lastName(),
        ];
    }
}
