<?php

namespace Database\Factories\Company\Ecommerce;

use App\Models\Company\District;
use App\Models\Company\Mahalla;
use App\Models\Company\Region;
use Illuminate\Database\Eloquent\Factories\Factory;

class DeliveryPointFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name_uz' => $this->faker->text(),
            'name_ru' => $this->faker->text(),
            'name_en' => $this->faker->text(),
            'name_oz' => $this->faker->text(),
            'description_uz' => $this->faker->text(),
            'description_ru' => $this->faker->text(),
            'description_en' => $this->faker->text(),
            'description_oz' => $this->faker->text(),
            'is_prepaid' => $this->faker->boolean(),
            'phone' => $this->faker->e164PhoneNumber(),
            'address_uz' => $this->faker->text(),
            'address_ru' => $this->faker->text(),
            'address_en' => $this->faker->text(),
            'address_oz' => $this->faker->text(),
            'landmark_uz' => $this->faker->text(),
            'landmark_ru' => $this->faker->text(),
            'landmark_en' => $this->faker->text(),
            'landmark_oz' => $this->faker->text(),
            'location_url' => $this->faker->url(),
            'lat' => $this->faker->longitude(),
            'lng' => $this->faker->latitude(),
            'region_id' => Region::factory(),
            'district_id' => District::factory(),
            'mahalla_id' => Mahalla::factory(),
        ];
    }
}
