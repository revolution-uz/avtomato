<?php

namespace Database\Factories\Company;

use App\Models\Company\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PersonalAccessTokenFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'token' => $this->faker->uuid(),
            'abilities' => ['*'],
            'tokenable_id' => User::factory(),
            'tokenable_type' => User::class,
            'device_ip' => $this->faker->ipv4(),
            'device_type' => $this->faker->word(),
            'device_os' => $this->faker->word(),
        ];
    }
}
