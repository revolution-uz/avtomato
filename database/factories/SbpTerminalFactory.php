<?php

namespace Database\Factories;

use App\Models\SbpTerminal;
use Illuminate\Database\Eloquent\Factories\Factory;

class SbpTerminalFactory extends Factory
{
    protected $model = SbpTerminal::class;

    public function definition(): array
    {
        return [
            'company_id' => $this->faker->randomNumber(),
            'terminal_id' => $this->faker->randomNumber(),
            'account' => $this->faker->word(),
            'filial_code' => $this->faker->word(),
            'inn' => $this->faker->word(),
            'name' => $this->faker->name(),
            'status' => $this->faker->randomNumber(),
        ];
    }
}
