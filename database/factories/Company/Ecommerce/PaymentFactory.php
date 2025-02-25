<?php

namespace Database\Factories\Company\Ecommerce;

use App\Enums\Company\PaymentStatusEnum;
use App\Models\Company\CompanyAdmin;
use App\Models\Company\Ecommerce\Order;
use App\Models\Company\Ecommerce\Payment;
use App\Models\Company\Ecommerce\User;
use App\Models\Company\TransactionType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'amount' => $this->faker->randomFloat(2, 10, 1000),
            'card_pan' => $this->faker->creditCardNumber,
            'status' => PaymentStatusEnum::randomValue(),
            'paid_at' => Carbon::now(),
            'external_id' => $this->faker->uuid,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'order_id' => Order::factory(),
            'user_id' => User::factory(),
            'transaction_type_id' => TransactionType::factory(),
            'admin_id' => CompanyAdmin::factory(),
        ];
    }

    public function notPaid(): PaymentFactory
    {
        return $this->state(fn (array $attributes) => [
            'status' => PaymentStatusEnum::WAITING_FOR_CONFIRMATION,
            'paid_at' => null,
        ]);
    }

    public function paid(): PaymentFactory
    {
        return $this->state(fn (array $attributes) => [
            'status' => PaymentStatusEnum::SUCCESS,
            'paid_at' => Carbon::now(),
        ]);
    }
}
