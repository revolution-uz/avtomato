<?php

namespace Database\Factories\Company;

use App\Enums\Company\LogType;
use App\Models\Company\CompanyAdmin;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class ActivityLogFactory extends Factory
{
    public function definition(): array
    {
        return [
            'log_name' => $this->faker->word(),
            'description' => $this->faker->text(50),
            'subject_type' => CompanyAdmin::class,
            'subject_id' => CompanyAdmin::factory(),
            'event' => LogType::randomValue(),
            'causer_type' => CompanyAdmin::class,
            'causer_id' => CompanyAdmin::factory(),
            'properties' => [
                $this->faker->word(),
            ],
            'batch_uuid' => null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
