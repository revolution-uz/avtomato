<?php

namespace Database\Factories\Company;

use App\Models\Company\Branch;
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
            'card_pan' => substr_replace($this->faker->creditCardNumber(), '******', 6, 6),
            'transaction_type_id' => TransactionType::factory(),
            'external_id' => $this->faker->unique()->numberBetween(1000, 9999),
            'amount' => $this->faker->randomFloat(),
            'branch_id' => $this->branch(),
            'paid_at' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }

    public function branch()
    {
        return Branch::factory(5)->create()->random()->id;
    }
}
