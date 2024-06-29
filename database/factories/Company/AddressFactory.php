<?php

namespace Database\Factories\Company;

use App\Enums\Company\AddressTypeEnum;
use App\Models\Company\Address;
use App\Models\Company\Customer;
use App\Models\Company\District;
use App\Models\Company\Mahalla;
use App\Models\Company\Region;
use Illuminate\Database\Eloquent\Factories\Factory;

class AddressFactory extends Factory
{
    protected $model = Address::class;

    public function definition(): array
    {
        return [
            'type' => AddressTypeEnum::randomValue(),
            'address' => $this->faker->address(),
            'region_id' => Region::factory(),
            'district_id' => District::factory(),
            'mahalla_id' => Mahalla::factory(),
            'addressable_type' => Customer::class,
            'addressable_id' => Customer::factory(),
        ];
    }
}
