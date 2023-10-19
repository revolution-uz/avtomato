<?php

namespace Database\Factories\Company;

use App\Enums\GenderEnum;
use App\Models\Company\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        return [
            'pinfl' => $this->faker->regexify('[0-9]{14}'),
            'tin' => $this->faker->randomNumber(9),
            'passport_series' => $this->faker->regexify('[A-Z]{2}'),
            'passport_number' => $this->faker->randomNumber(7),
            'passport_given_date' => Carbon::now(),
            'passport_expiration_date' => Carbon::now(),
            'passport_given_place' => $this->faker->city(),
            'passport_citizenship' => $this->faker->city(),
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'middle_name' => $this->faker->name(),
            'birth_date' => Carbon::now(),
            'birth_place' => $this->faker->city(),
            'gender' => array_rand(GenderEnum::cases()),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
