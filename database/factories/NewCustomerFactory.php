<?php

namespace Database\Factories;

use App\Models\NewCustomer;
use Illuminate\Database\Eloquent\Factories\Factory;

class NewCustomerFactory extends Factory
{
    protected $model = NewCustomer::class;

    public function definition(): array
    {
        return [
            'full_name' => $this->faker->name(),
            'phone' => $this->faker->e164PhoneNumber(),
            'is_checked' => $this->faker->boolean(),
            'comment' => $this->faker->text(512),
        ];
    }
}
