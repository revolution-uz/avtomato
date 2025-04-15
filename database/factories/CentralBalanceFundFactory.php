<?php

namespace Database\Factories;

use App\Models\CentralBalanceFund;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class CentralBalanceFundFactory extends Factory
{
    protected $model = CentralBalanceFund::class;

    public function definition(): array
    {
        return [
            'amount' => $this->faker->numberBetween(1000, pow(10, 8)),
            'phone_number' => $this->faker->optional()->e164PhoneNumber(),
            'external_transaction_id' => $this->faker->optional()->uuid(),
            'terminal_id' => $this->faker->optional()->uuid(),
            'merchant_id' => $this->faker->optional()->uuid(),
            'card_pan' => $this->faker->optional()->creditCardNumber(),
            'company_id' => Company::query()->inRandomOrder()->value('id'),
            'balance_fund_id' => $this->faker->numberBetween(1, 100),
        ];
    }
}
