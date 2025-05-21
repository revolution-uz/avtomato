<?php

namespace Database\Factories\Company\Ecommerce;

use App\Models\Company\Ecommerce\EcommerceImage;
use App\Models\Company\Ecommerce\ProductOption;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductOptionValueFactory extends Factory
{
    public function definition(): array
    {
        $valueEn = $this->faker->unique()->word;

        return [
            'value_uz' => $this->faker->word,
            'value_ru' => $this->faker->word,
            'value_en' => $valueEn,
            'value_oz' => $this->faker->word,
            'slug' => Str::slug($valueEn),

            'option_id' => ProductOption::factory(),
            'image_id' => EcommerceImage::factory(),
        ];
    }
}
