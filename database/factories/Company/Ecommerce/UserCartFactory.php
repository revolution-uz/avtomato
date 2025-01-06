<?php

namespace Database\Factories\Company\Ecommerce;

use App\Models\Company\Ecommerce\ProductVariation;
use App\Models\Company\Ecommerce\User;
use App\Models\Company\Ecommerce\UserCart;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserCartFactory extends Factory
{
    protected $model = UserCart::class;

    public function definition(): array
    {
        /** @var ProductVariation $variation */
        $variation = ProductVariation::factory()->create();

        return [
            'quantity' => $this->faker->numberBetween(1, 10),
            'user_id' => User::factory(),
            'product_id' => $variation->product_id,
            'variation_id' => $variation->id,
        ];
    }
}
