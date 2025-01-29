<?php

namespace Database\Factories\Company\Ecommerce;

use App\Models\Company\Ecommerce\Product;
use App\Models\Company\Ecommerce\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFavouriteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'product_id' => Product::factory(),
        ];
    }
}
