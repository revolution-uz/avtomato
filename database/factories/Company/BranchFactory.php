<?php

namespace Database\Factories\Company;

use App\Models\Company\Branch;
use Illuminate\Database\Eloquent\Factories\Factory;

class BranchFactory extends Factory
{
    protected $model = Branch::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'address' => $this->faker->address(),
            'inn' => $this->faker->randomNumber(9),
            'created_at' => time(),
            'updated_at' => time(),
        ];
    }
}
