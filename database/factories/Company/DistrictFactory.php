<?php

namespace Database\Factories\Company;

use App\Models\Company\District;
use App\Models\Company\Region;
use Illuminate\Database\Eloquent\Factories\Factory;

class DistrictFactory extends Factory
{
    protected $model = District::class;

    public function definition(): array
    {
        return [
            'oilakredit_id' => $this->faker->unique()->numberBetween(1, 1000),
            'name_oz' => $this->faker->name(),
            'name_uz' => $this->faker->name(),
            'name_ru' => $this->faker->name(),
            'name_en' => $this->faker->name(),

            'region_id' => Region::factory(),
        ];
    }
}
