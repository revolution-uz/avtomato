<?php

namespace Database\Factories\Company;

use App\Models\Company\Phone;
use Illuminate\Database\Eloquent\Factories\Factory;

class PhoneFactory extends Factory
{
    protected $model = Phone::class;

    public function definition(): array
    {
        return [
            'phone' => $this->faker->e164PhoneNumber(),
        ];
    }
}
