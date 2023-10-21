<?php

namespace Database\Factories\Company;

use App\Models\Company\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    public function definition(): array
    {
        return [
            'installment_id' => $this->faker->randomNumber(),
            'customer_id' => $this->faker->randomNumber(),
            'card_id' => $this->faker->randomNumber(),
            'transaction_type_id' => $this->faker->randomNumber(),
            'myuzkard_id' => $this->faker->randomNumber(),
            'amount' => $this->faker->randomFloat(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
