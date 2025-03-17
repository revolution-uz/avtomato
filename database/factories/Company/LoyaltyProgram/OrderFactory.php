<?php

namespace Database\Factories\Company\LoyaltyProgram;

use App\Enums\Company\LoyaltyProgramm\DiscountTypeEnum;
use App\Enums\Company\LoyaltyProgramm\OrderStatusEnum;
use App\Models\Company\CompanyAdmin;
use App\Models\Company\LoyaltyProgram\Order;
use App\Models\Company\LoyaltyProgram\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'status' => $this->faker->randomElement(OrderStatusEnum::cases()),
            'discount_type' => $this->faker->randomElement(DiscountTypeEnum::cases()),
            'discount_value' => $this->faker->randomFloat(2, 0, 100),
            'discount_amount' => $this->faker->randomFloat(2, 0, 100),
            'total_amount' => $this->faker->randomFloat(2, 0, 1000),
            'total_discount_amount' => $this->faker->randomFloat(2, 0, 100),
            'total_paid_amount' => $this->faker->randomFloat(2, 0, 1000),

            'user_id' => User::factory(),
            'admin_id' => $this->faker->boolean ? CompanyAdmin::factory() : null,
        ];
    }
}
