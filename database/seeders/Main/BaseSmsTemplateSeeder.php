<?php

namespace Database\Seeders\Main;

use App\Enums\SmsTemplateEnum;
use App\Models\BaseSmsTemplate;
use Illuminate\Database\Seeder;

class BaseSmsTemplateSeeder extends Seeder
{
    public function run(): void
    {
        foreach (SmsTemplateEnum::cases() as $case) {
            BaseSmsTemplate::query()->updateOrCreate(['id' => $case->value], $case->getDefaultTemplate());
        }

        BaseSmsTemplate::query()->whereNotIn('id', SmsTemplateEnum::cases())->forceDelete();
    }
}
