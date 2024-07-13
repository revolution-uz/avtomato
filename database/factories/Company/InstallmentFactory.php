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
            'amount' => $this->faker->randomFloat(),
            'initial_fee' => $this->faker->randomFloat(),
            'description' => $this->faker->text(),
            'sms_notify' => $this->faker->boolean(),
            'any_card_charge' => $this->faker->boolean(),
            'is_blacklist' => $this->faker->boolean(),
            'status' => InstallmentStatusEnum::randomValue(),
            'period' => $this->faker->randomDigit(),
            'plum_id' => $this->faker->word(),
            'starts_in' => Carbon::now(),
            'next_pay_date' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
