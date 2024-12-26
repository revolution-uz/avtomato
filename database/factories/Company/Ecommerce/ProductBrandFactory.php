<?php

namespace Database\Factories\Company\Ecommerce;

use App\Models\Company\Ecommerce\EcommerceImage;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductBrandFactory extends Factory
{
    public function definition(): array
    {
        $nameEn = $this->faker->unique()->word;

        return [
            'name_uz' => $this->faker->word,
            'name_ru' => $this->faker->word,
            'name_oz' => $this->faker->word,
            'name_en' => $nameEn,
            'is_active' => $this->faker->boolean,
            'slug' => Str::slug($nameEn),

            'image_id' => EcommerceImage::factory(),
        ];
    }
}
