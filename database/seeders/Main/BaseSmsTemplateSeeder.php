<?php

namespace Database\Seeders\Main;

use App\Enums\Company\SmsTemplateEnum;
use App\Models\BaseSmsTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class BaseSmsTemplateSeeder extends Seeder
{
    public function run(): void
    {
        foreach (SmsTemplateEnum::cases() as $case) {
            BaseSmsTemplate::query()->updateOrCreate(['id' => $case->value], $case->getDefaultTemplate());
        }

        BaseSmsTemplate::query()->whereNotIn('id', SmsTemplateEnum::cases())->forceDelete();

        Log::info('BaseSmsTemplateSeeder completed successfully.');
    }
}
