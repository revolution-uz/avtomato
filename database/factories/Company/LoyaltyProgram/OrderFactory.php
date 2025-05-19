<?php

namespace Database\Factories\Company\LoyaltyProgram;

use App\Enums\Company\LoyaltyProgram\OrderStatusEnum;
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
            'user_id' => User::factory(),
        ];
    }
}
