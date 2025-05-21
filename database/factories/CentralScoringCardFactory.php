<?php

namespace Database\Factories;

use App\Models\CentralScoringCard;
use Illuminate\Database\Eloquent\Factories\Factory;

class CentralScoringCardFactory extends Factory
{
    protected $model = CentralScoringCard::class;

    public function definition(): array
    {
        return [
            'number' => $this->faker->creditCardNumber,
            'expiry' => $this->faker->date('ym'),
            'is_verified' => $this->faker->boolean(),
            'external_id' => $this->faker->word(),
        ];
    }

    public function configure(): static
    {
        return $this->afterMaking(function (CentralScoringCard $card) {
            $card->hash = (int) $card->number;
        });
    }
}
