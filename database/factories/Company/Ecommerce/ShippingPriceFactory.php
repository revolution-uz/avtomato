<?php

namespace Database\Factories\Company\Ecommerce;

use App\Models\Company\District;
use App\Models\Company\Mahalla;
use App\Models\Company\Region;
use Illuminate\Database\Eloquent\Factories\Factory;

class ShippingPriceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'price' => $this->faker->randomFloat(2),

            'region_id' => Region::factory(),
            'district_id' => District::factory(),
            'mahalla_id' => Mahalla::factory(),
        ];
    }
}
