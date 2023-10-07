<?php

namespace Database\Factories\Company;

use App\Models\Company\District;
use App\Models\Company\Mahalla;
use Illuminate\Database\Eloquent\Factories\Factory;

class MahallaFactory extends Factory
{
    protected $model = Mahalla::class;

    public function definition(): array
    {
        return [
            'oilakredit_id' => $this->faker->unique()->numberBetween(1, 1000),
            'name_oz' => $this->faker->name(),
            'name_uz' => $this->faker->name(),
            'name_ru' => $this->faker->name(),
            'name_en' => $this->faker->name(),

            'district_id' => District::factory(),
        ];
    }
}
