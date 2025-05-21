<?php

namespace Database\Factories\Company;

use App\Enums\Company\SmsMessageCustomerTypeEnum;
use App\Enums\Company\SmsMessageStatusEnum;
use App\Models\Company\Customer;
use App\Models\Company\Installment;
use App\Models\Company\SmsMessage;
use Illuminate\Database\Eloquent\Factories\Factory;

class SmsMessageFactory extends Factory
{
    protected $model = SmsMessage::class;

    public function definition(): array
    {
        return [
            'phone' => $this->faker->regexify('[1-9]{12}'),
            'message' => $this->faker->text,
            'status' => SmsMessageStatusEnum::randomValue(),
            'ext_id' => $this->faker->randomNumber(9),
            'installment_id' => Installment::factory(),
            'customer_id' => Customer::factory(),
            'customer_type' => SmsMessageCustomerTypeEnum::randomValue(),
        ];
    }
}
