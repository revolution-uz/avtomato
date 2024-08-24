<?php

namespace Database\Seeders\Company;

use App\Models\BaseSmsTemplate;
use App\Models\Company\SmsTemplate;
use Illuminate\Database\Seeder;

class SmsTemplateSeeder extends Seeder
{
    public function run(): void
    {
        /** @var BaseSmsTemplate $baseSmsTemplate */
        foreach (BaseSmsTemplate::query()->get()->all() as $baseSmsTemplate) {
            SmsTemplate::query()->firstOrCreate([
                'id' => $baseSmsTemplate->id,
            ], [
                'original_template' => $baseSmsTemplate->original_template,
            ]);
        }

        SmsTemplate::query()->whereNotIn('id', BaseSmsTemplate::query()->pluck('id'))->forceDelete();
    }
}
