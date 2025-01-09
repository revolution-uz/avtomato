<?php

namespace Database\Factories;

use App\Enums\TelegramChannelTypeEnum;
use App\Models\TelegramChannel;
use Illuminate\Database\Eloquent\Factories\Factory;

class TelegramChannelFactory extends Factory
{
    protected $model = TelegramChannel::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'chat_id' => $this->faker->randomNumber(),
            'is_active' => $this->faker->boolean(),
            'type' => TelegramChannelTypeEnum::randomValue(),
            'company_id' => tenant('id'),
        ];
    }
}
