<?php

namespace Database\Factories;

use App\Enums\Company\CardTypeEnum;
use App\Models\CentralCustomer;
use App\Models\CentralUniredCard;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class CentralUniredCardFactory extends Factory
{
    protected $model = CentralUniredCard::class;

    public function definition(): array
    {
        return [
            'uuid' => $this->faker->uuid(),
            'pinfl' => $this->faker->regexify('[0-9]{14}'),
            'auto' => $this->faker->boolean(),
            'is_verified' => $this->faker->boolean(),
            'is_blocked' => $this->faker->boolean(),
            'block_reason' => $this->faker->word(),
            'blocked_at' => Carbon::now(),
            'owner' => $this->faker->word(),
            'pan' => $this->faker->word(),
            'phone' => $this->faker->phoneNumber(),
            'type' => $this->faker->randomElement(CardTypeEnum::values()),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'customer_id' => CentralCustomer::factory(),
        ];
    }
}
