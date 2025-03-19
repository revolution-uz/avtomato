<?php

namespace Database\Factories\Company\LoyaltyProgram;

use App\Enums\Company\LoyaltyProgramm\OrderPaymentStatusEnum;
use App\Models\Company\CompanyAdmin;
use App\Models\Company\LoyaltyProgram\Order;
use App\Models\Company\LoyaltyProgram\OrderPayment;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderPaymentFactory extends Factory
{
    protected $model = OrderPayment::class;

    public function definition()
    {
        return [
            'payment_method' => $this->faker->numberBetween(0, 3),
            'status' => $this->faker->randomElement(OrderPaymentStatusEnum::cases())->value,
            'amount' => $this->faker->randomFloat(2, 10, 1000),

            'order_id' => Order::factory(),
            'admin_id' => $this->faker->boolean ? CompanyAdmin::factory() : null,
        ];
    }
}
