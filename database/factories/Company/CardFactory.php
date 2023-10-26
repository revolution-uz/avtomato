<?php

namespace Database\Factories\Company;

use App\Enums\Company\CardStatusEnum;
use App\Models\Company\Card;
use App\Models\Company\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class CardFactory extends Factory
{
    protected $model = Card::class;

    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'pan' => $this->faker->randomDigit(),
            'number' => $this->faker->word(),
            'expiry' => $this->faker->randomDigit(),
            'status' => CardStatusEnum::randomValue(),
            'balance' => $this->faker->randomFloat(),
            'plum_id' => $this->faker->randomNumber(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
