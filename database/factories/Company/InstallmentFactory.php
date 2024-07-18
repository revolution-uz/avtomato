<?php

namespace Database\Factories\Company;

use App\Enums\Company\InstallmentStatusEnum;
use App\Enums\Company\InstallmentTypeEnum;
use App\Models\Company\Branch;
use App\Models\Company\CompanyAdmin;
use App\Models\Company\Customer;
use App\Models\Company\Installment;
use App\Models\Company\Tariff;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class InstallmentFactory extends Factory
{
    protected $model = Installment::class;

    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'branch_id' => Branch::factory(),
            'tariff_id' => Tariff::factory(),
            'admin_id' => CompanyAdmin::factory(),
            'type' => InstallmentTypeEnum::randomValue(),
            'amount' => $this->faker->randomFloat(nbMaxDecimals: 0, min: 1000000),
            'percent' => $this->faker->randomFloat(nbMaxDecimals: 1, max: 20),
            'initial_fee' => $this->faker->randomFloat(nbMaxDecimals: 2, max: 100000),
            'description' => $this->faker->text(),
            'sms_notify' => $this->faker->boolean(),
            'any_card_charge' => $this->faker->boolean(),
            'is_blacklist' => $this->faker->boolean(),
            'status' => InstallmentStatusEnum::randomValue(),
            'period' => $this->faker->randomDigitNot(0),
            'plum_id' => $this->faker->word(),
            'starts_in' => Carbon::tomorrow(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
