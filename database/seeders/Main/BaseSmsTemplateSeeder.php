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
            /** @var BaseSmsTemplate $template */
            BaseSmsTemplate::query()->updateOrCreate([
                'id' => $case->value,
            ], [
                'original_template' => $case->getDefaultTemplate(),
            ]);
        }

        BaseSmsTemplate::query()->whereNotIn('id', SmsTemplateEnum::cases())->forceDelete();
    }
}
