<?php

namespace Database\Factories\Company\LoyaltyProgram;

use App\Enums\Company\LoyaltyProgram\OrderStatusEnum;
use App\Models\Company\CompanyAdmin;
use App\Models\Company\LoyaltyProgram\Order;
use App\Models\Company\LoyaltyProgram\OrderStatusHistory;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderStatusHistoryFactory extends Factory
{
    protected $model = OrderStatusHistory::class;

    public function definition()
    {
        return [
            'from_status' => $this->faker->randomElement(OrderStatusEnum::cases()),
            'to_status' => $this->faker->randomElement(OrderStatusEnum::cases()),

            'order_id' => Order::factory(),
            'admin_id' => $this->faker->boolean ? CompanyAdmin::factory() : null,
        ];
    }
}
