<?php

namespace Database\Factories\Company;

use App\Enums\Company\TransactionAttemptStatusEnum;
use App\Models\Company\SbpTransaction;
use Illuminate\Database\Eloquent\Factories\Factory;

class SbpTransactionFactory extends Factory
{
    protected $model = SbpTransaction::class;

    public function definition()
    {
        return [
            'terminal_id' => $this->faker->numberBetween(1, 1000),
            'ext_id' => $this->faker->uuid,
            'amount' => $this->faker->randomFloat(2, 0, 10000),
            'credit' => $this->faker->boolean()
                ? [
                    'ext_id' => $this->faker->uuid(),
                    'state' => $this->faker->optional()->numberBetween(0, 5),
                    'amount' => $this->faker->randomFloat(2, 0, 10000),
                    'currency' => $this->faker->currencyCode(),
                ]
                : null,
            'debit' => $this->faker->boolean()
                ? [
                    'operation_id' => $this->faker->optional()->randomNumber(),
                    'order_id' => $this->faker->optional()->uuid(),
                    'state' => $this->faker->optional()->word(),
                    'description' => $this->faker->optional()->sentence(),
                    'amount' => $this->faker->optional()->numberBetween(1, 100000),
                    'commission' => $this->faker->optional()->randomFloat(2, 0, 1000),
                    'currency' => $this->faker->optional()->numberBetween(1, 999),
                    'url' => $this->faker->optional()->url(),
                ]
                : null,
            'sender' => $this->faker->boolean()
                ? [
                    'number' => $this->faker->optional()->regexify('[0-9]{16}'),
                    'expire' => $this->faker->optional()->creditCardExpirationDateString(),
                ]
                : null,
            'receiver' => $this->faker->boolean()
                ? [
                    'owner' => $this->faker->optional()->name(),
                    'expire' => $this->faker->optional()->date('m/y'),
                ]
                : null,
            'currency' => $this->faker->optional()->randomFloat(2, 0, 10000),
            'payment_updated_at' => $this->faker->optional()->dateTime,
            'status' => $this->faker->randomElement(TransactionAttemptStatusEnum::cases()),
        ];
    }
}
