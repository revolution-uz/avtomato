<?php

namespace Database\Factories\Company;

use App\Models\Company\TransactionType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class TransactionTypeFactory extends Factory
{
    protected $model = TransactionType::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'name_uz' => $this->faker->name(),
            'name_ru' => $this->faker->name(),
            'name_en' => $this->faker->name(),
            'is_system' => $this->faker->boolean(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
