<?php

namespace Database\Factories;

use App\Enums\Company\CompanyStatusEnum;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyFactory extends Factory
{
    protected $model = Company::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word,
            'owner_phone' => $this->faker->e164PhoneNumber,
            'owner_name' => $this->faker->firstNameMale . ' ' . $this->faker->lastName,
            'status' => CompanyStatusEnum::CREATED,
        ];
    }
}
