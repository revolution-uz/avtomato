<?php

namespace Database\Factories\Company;

use App\Enums\Company\TransactionAttemptStatusEnum;
use App\Enums\Company\TransactionAttemptTypeEnum;
use App\Models\Company\Card;
use App\Models\Company\Customer;
use App\Models\Company\Installment;
use App\Models\Company\Transaction;
use App\Models\Company\TransactionAttempt;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionAttemptFactory extends Factory
{
    protected $model = TransactionAttempt::class;

    public function definition(): array
    {
        return [
            'installment_id' => Installment::factory(),
            'customer_id' => Customer::factory(),
            'card_id' => Card::factory(),
            'transaction_id' => Transaction::factory(),
            'amount' => $this->faker->randomFloat(1000, 9999),
            'status' => TransactionAttemptStatusEnum::randomValue(),
            'type' => TransactionAttemptTypeEnum::randomValue(),
        ];
    }
}
