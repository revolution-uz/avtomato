<?php

namespace Database\Factories\Company;

use App\Enums\GenderEnum;
use App\Models\Company\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        return [
            'pinfl' => $this->faker->regexify('[0-9]{14}'),
            'tin' => $this->faker->regexify('[0-9]{9}'),
            'passport_series' => $this->faker->regexify('[A-Z]{2}'),
            'passport_number' => $this->faker->regexify('[0-9]{7}'),
            'passport_given_date' => Carbon::now(),
            'passport_expiration_date' => Carbon::now(),
            'passport_given_place' => $this->faker->city(),
            'passport_citizenship' => Str::substr($this->faker->country(), 0, 32),
            'first_name' => Str::substr($this->faker->unique()->firstName(), 0, 32),
            'last_name' => Str::substr($this->faker->lastName(), 0, 32),
            'middle_name' => Str::substr($this->faker->name(), 0, 32),
            'birth_date' => Carbon::now(),
            'birth_place' => Str::substr($this->faker->city(), 0, 32),
            'gender' => array_rand(GenderEnum::cases()),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
