<?php

namespace Database\Factories\Company\LoyaltyProgram;

use App\Enums\Company\LoyaltyProgramm\ShopStatusEnum;
use App\Models\Company\LoyaltyProgram\Shop;
use Illuminate\Database\Eloquent\Factories\Factory;

class ShopFactory extends Factory
{
    protected $model = Shop::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company,
            'address' => $this->faker->address,
            'lat' => $this->faker->latitude,
            'lng' => $this->faker->longitude,
            'status' => $this->faker->randomElement(ShopStatusEnum::cases()),
            'external_code' => $this->faker->unique()->uuid(),
            'external_name' => $this->faker->company,
            'external_address' => $this->faker->address,
        ];
    }
}
