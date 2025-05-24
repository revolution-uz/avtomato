<?php

namespace Database\Factories\Company;

use App\Models\Company\CompanyAdmin;
use Illuminate\Database\Eloquent\Factories\Factory;

class BalanceFundFactory extends Factory
{
    public function definition(): array
    {
        return [
            'amount' => $this->faker->numberBetween(1, 10000),
            'phone_number' => $this->faker->e164PhoneNumber(),
            'external_transaction_id' => $this->faker->optional()->numberBetween(1, 1000000),
            'terminal_id' => $this->faker->optional()->uuid(),
            'merchant_id' => $this->faker->optional()->uuid(),
            'card_number' => $this->faker->creditCardNumber(),
        ];
    }

    public function withAdmin(): static
    {
        return $this->state(fn (array $attributes) => [
            'admin_id' => CompanyAdmin::factory(),
        ]);
    }
}
