<?php

namespace Database\Factories\Company\LoyaltyProgram;

use App\Models\Company\LoyaltyProgram\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'phone' => $this->faker->e164PhoneNumber,
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'birth_date' => $this->faker->date,
            'main_device' => $this->faker->randomElement(['SN1234567890', 'SN0987654321', 'SN5678901234']),
        ];
    }
}
