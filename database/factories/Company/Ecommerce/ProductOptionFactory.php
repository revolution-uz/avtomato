<?php

namespace Database\Factories\Company\Ecommerce;

use App\Enums\Company\OptionTypeEnum;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductOptionFactory extends Factory
{
    public function definition(): array
    {
        $nameEn = $this->faker->unique()->word;

        return [
            'name_uz' => $this->faker->word,
            'name_ru' => $this->faker->word,
            'name_en' => $nameEn,
            'name_oz' => $this->faker->word,
            'slug' => Str::slug($nameEn),
            'description' => $this->faker->word,
            'type' => OptionTypeEnum::randomValue(),
        ];
    }
}
