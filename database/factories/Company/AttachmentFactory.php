<?php

namespace Database\Factories\Company;

use App\Enums\Company\AttachmentTypeEnum;
use App\Models\Company\CompanyAdmin;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttachmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'type' => AttachmentTypeEnum::randomValue(),
            'path' => $this->faker->word(),
            'name' => $this->faker->word(),
            'original_name' => $this->faker->word(),
            'mime' => $this->faker->word(),
            'attachmentable_id' => CompanyAdmin::class,
            'attachmentable_type' => CompanyAdmin::factory(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}