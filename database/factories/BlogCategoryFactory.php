<?php

namespace Database\Factories;

use App\Enums\MessageField;
use App\Models\BlogCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class BlogCategoryFactory extends Factory
{
    protected $model = BlogCategory::class;

    public function definition(): array
    {
        return [
            'name_en' => $this->faker->name(),
            'name_ru' => $this->faker->name(),
            'name_uz' => $this->faker->name(),
            'slug' => $this->faker->slug(),
            'meta_description' => MessageField::from([
                'message_en' => $this->faker->text(255),
                'message_ru' => $this->faker->text(255),
                'message_uz' => $this->faker->text(255),
            ]),
            'meta_keywords' => MessageField::from([
                'message_en' => $this->faker->text(255),
                'message_ru' => $this->faker->text(255),
                'message_uz' => $this->faker->text(255),
            ]),
        ];
    }
}
