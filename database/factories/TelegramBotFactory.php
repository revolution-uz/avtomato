<?php

namespace Database\Factories;

use App\Models\TelegramBot;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class TelegramBotFactory extends Factory
{
    protected $model = TelegramBot::class;

    public function definition(): array
    {
        return [
            'token' => Str::random(10),
            'username' => $this->faker->userName(),
            'last_used_at' => Carbon::now(),
        ];
    }
}
