<?php

namespace Database\Factories\Company\Ecommerce;

use App\Models\Company\CompanyAdmin;
use App\Models\Company\Ecommerce\Order;
use App\Models\Company\Ecommerce\OrderStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderStatusHistoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'from_status_id' => OrderStatus::factory(),
            'to_status_id' => OrderStatus::factory(),
            'admin_id' => CompanyAdmin::factory(),
            'order_id' => Order::factory(),
        ];
    }
}
