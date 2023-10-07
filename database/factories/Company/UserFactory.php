<?php

namespace Database\Factories\Company;

use App\Enums\Company\UserStatusEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Admin>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'status' => UserStatusEnum::ACTIVE,
            'avatar' => $this->faker->word(),
            'template_theme' => [],
            'language' => $this->faker->word(),
            'username' => $this->faker->userName(),
            'password' => bcrypt($this->faker->password()),
            'created_at' => $this->faker->randomNumber(),
            'updated_at' => $this->faker->randomNumber(),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
