<?php

namespace Database\Factories\Company\Ecommerce;

use App\Models\Company\Ecommerce\EcommerceImage;
use App\Models\Company\Ecommerce\ProductCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductCategoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name_uz' => $this->faker->text(255),
            'name_ru' => $this->faker->text(255),
            'name_en' => $this->faker->text(255),
            'name_oz' => $this->faker->text(255),
            'is_active' => $this->faker->boolean,
            'image_id' => EcommerceImage::factory(),
        ];
    }

    public function withParent(): static
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => ProductCategory::factory(),
        ]);
    }
}
