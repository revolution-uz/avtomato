<?php

namespace Database\Factories\Company;

use App\Enums\Company\CardTypeEnum;
use App\Models\Company\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class UniredCardFactory extends Factory
{
    public function definition(): array
    {
        return [
            'pinfl' => $this->faker->unique()->regexify('[0-9]{14}'),
            'uuid' => $this->faker->unique()->uuid(),
            'auto' => $this->faker->boolean(),
            'is_verified' => $this->faker->boolean(),
            'is_blocked' => $this->faker->boolean(),
            'block_reason' => $this->faker->optional()->sentence(),
            'blocked_at' => $this->faker->optional()->dateTime(),
            'owner' => $this->faker->name(),
            'pan' => $this->faker->creditCardNumber(),
            'phone' => $this->faker->e164PhoneNumber(),
            'type' => $this->faker->randomElement(CardTypeEnum::values()),

            'customer_id' => Customer::factory(),
        ];
    }
}
