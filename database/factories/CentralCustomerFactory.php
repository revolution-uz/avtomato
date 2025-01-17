<?php

namespace Database\Factories;

use App\Models\CentralCustomer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class CentralCustomerFactory extends Factory
{
    protected $model = CentralCustomer::class;

    public function definition(): array
    {
        return [
            'pinfl' => $this->faker->regexify('[0-9]{14}'),
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'middle_name' => $this->faker->name(),
            'atmos_pinfl_searched_at' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
