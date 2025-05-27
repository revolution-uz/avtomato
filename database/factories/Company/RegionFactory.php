<?php

namespace Database\Factories\Company;

use App\Models\Company\Region;
use Illuminate\Database\Eloquent\Factories\Factory;

class RegionFactory extends Factory
{
    protected $model = Region::class;

    public function definition(): array
    {
        return [
            'oilakredit_id' => $this->faker->unique()->numberBetween(1, 1000),
            'name_oz' => $this->faker->name(),
            'name_uz' => $this->faker->name(),
            'name_ru' => $this->faker->name(),
            'name_en' => $this->faker->name(),
        ];
    }
}
