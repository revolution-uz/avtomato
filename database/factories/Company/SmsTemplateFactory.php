<?php

namespace Database\Factories\Company;

use App\Models\Company\SmsTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

class SmsTemplateFactory extends Factory
{
    protected $model = SmsTemplate::class;

    public function definition(): array
    {
        return [
            'original_template' => $this->faker->text,
            'template' => $this->faker->text,
            'data' => null,
            'description' => $this->faker->text,
        ];
    }
}
