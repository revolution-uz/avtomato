<?php

namespace Database\Factories\Company;

use App\Enums\Company\CompanyStatusEnum;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyFactory extends Factory
{
    protected $model = Company::class;

    public function definition(): array
    {
        $name = $this->faker->name();
        return [
            'name' => $name,
            'owner_phone' => $this->faker->phoneNumber(),
            'owner_name' => $this->faker->name(),
            'subdomain' => \Str::slug($name),
            'db_user' => $this->faker->word(),
            'db_name' => $this->faker->name(),
            'db_password' => \Str::random(),
            'status' => CompanyStatusEnum::CREATED
        ];
    }
}
