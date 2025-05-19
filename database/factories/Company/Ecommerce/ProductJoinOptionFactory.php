<?php

namespace Database\Factories\Company\Ecommerce;

use App\Models\Company\Ecommerce\Product;
use App\Models\Company\Ecommerce\ProductOption;
use App\Models\Company\Ecommerce\ProductOptionValue;
use App\Models\Company\Ecommerce\ProductVariation;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductJoinOptionFactory extends Factory
{
    public function definition(): array
    {
        $product = Product::factory()->create();
        $option = ProductOption::factory()->create();

        return [
            'option_id' => $option->id,
            'value_id' => ProductOptionValue::factory()->create(['option_id' => $option->id]),
            'product_id' => $product->id,
            'product_variation_id' => ProductVariation::factory()->create(['product_id' => $product->id]),
        ];
    }
}
