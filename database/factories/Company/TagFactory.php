<?php

namespace Database\Factories\Company;

use App\Models\Company\Tag;
use Illuminate\Database\Eloquent\Factories\Factory;

class TagFactory extends Factory
{
    protected $model = Tag::class;

    public function definition(): array
    {
        return [
            'name_uz' => $this->faker->name,
            'name_ru' => $this->faker->name,
            'name_en' => $this->faker->name,
            'name_oz' => $this->faker->name,
        ];
    }
}
