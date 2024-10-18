<?php

namespace Database\Factories;

use App\Enums\Company\CompanyStatusEnum;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CompanyFactory extends Factory
{
    protected $model = Company::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company,
            'owner_phone' => $this->faker->e164PhoneNumber,
            'owner_name' => $this->faker->name('male'),
            'status' => CompanyStatusEnum::randomValue(),
            'address' => $this->faker->address,
            'bank_name' => Str::ucfirst($this->faker->word()) . ' Bank',
            'bank_code' => $this->faker->numberBetween(10000, 99999),
            'account_number' => $this->faker->numberBetween(10000, 99999),
            'tin' => $this->faker->numberBetween(10000, 99999),
        ];
    }
}
