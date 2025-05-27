<?php

namespace Database\Factories\Company;

use App\Models\Company\CompanyAdmin;
use App\Models\Company\Installment;
use Illuminate\Database\Eloquent\Factories\Factory;

class InstallmentCommentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'admin_id' => CompanyAdmin::factory(),
            'installment_id' => Installment::factory(),
            'comment' => $this->faker->sentence,
        ];
    }
}
