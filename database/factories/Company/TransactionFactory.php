<?php

namespace Database\Factories\Company;

use App\Models\Company\Card;
use App\Models\Company\Customer;
use App\Models\Company\Installment;
use App\Models\Company\Transaction;
use App\Models\Company\TransactionType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    public function definition(): array
    {
        return [
            'installment_id' => Installment::factory(),
            'customer_id' => Customer::factory(),
            'card_id' => Card::factory(),
            'transaction_type_id' => TransactionType::factory(),
            'external_id' => $this->faker->unique()->numberBetween(1000, 9999),
            'amount' => $this->faker->randomFloat(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
