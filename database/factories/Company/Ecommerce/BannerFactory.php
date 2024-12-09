<?php

namespace Database\Factories\Company\Ecommerce;

use App\Models\Company\Ecommerce\EcommerceImage;
use App\Models\Company\Ecommerce\Product;
use App\Models\Company\Ecommerce\ProductBrand;
use App\Models\Company\Ecommerce\ProductCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class BannerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name_uz' => $this->faker->word,
            'name_ru' => $this->faker->word,
            'name_en' => $this->faker->word,
            'name_oz' => $this->faker->word,
            'is_active' => $this->faker->boolean,
            'url' => $this->faker->url,
            'image_uz_id' => EcommerceImage::factory(),
            'image_ru_id' => EcommerceImage::factory(),
            'image_en_id' => EcommerceImage::factory(),
            'image_oz_id' => EcommerceImage::factory(),
            'category_id' => ProductCategory::factory(),
            'brand_id' => ProductBrand::factory(),
            'product_id' => Product::factory(),
        ];
    }
}
