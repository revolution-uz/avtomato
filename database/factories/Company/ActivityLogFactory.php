<?php

namespace Database\Factories\Company;

use App\Enums\Company\LogType;
use App\Models\Company\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class ActivityLogFactory extends Factory
{
    public function definition(): array
    {
        return [
            'log_name' => $this->faker->word(),
            'description' => $this->faker->word(),
            'subject_type' => User::class,
            'subject_id' => User::factory(),
            'event' => LogType::randomValue(),
            'causer_type' => User::class,
            'causer_id' => User::factory(),
            'properties' => [
                $this->faker->word(),
            ],
            'batch_uuid' => null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
