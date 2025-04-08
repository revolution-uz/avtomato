<?php

namespace Database\Factories;

use App\Enums\MessageField;
use App\Models\Blog;
use App\Models\BlogCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class BlogFactory extends Factory
{
    protected $model = Blog::class;

    public function definition(): array
    {
        return [
            'slug' => $this->faker->slug(),
            'title_en' => $this->faker->text(255),
            'title_ru' => $this->faker->text(255),
            'title_uz' => $this->faker->text(255),
            'body_en' => $this->faker->text(1000),
            'body_ru' => $this->faker->text(1000),
            'body_uz' => $this->faker->text(1000),
            'image_en' => $this->faker->image(),
            'image_ru' => $this->faker->image(),
            'image_uz' => $this->faker->image(),
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
            'category_id' => BlogCategory::factory(),
        ];
    }

    public function withExistCategory(): static
    {
        $category = BlogCategory::query()->inRandomOrder()->first();

        return $this->state(fn (array $attributes) => [
            'category_id' => $category?->id ?: BlogCategory::factory(),
        ]);
    }
}
