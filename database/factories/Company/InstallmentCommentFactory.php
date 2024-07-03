<?php

namespace Database\Factories\Company;

use App\Models\Company\Installment;
use App\Models\Company\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class InstallmentCommentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'installment_id' => Installment::factory(),
            'comment' => $this->faker->sentence,
        ];
    }
}
