<?php

namespace Database\Seeders\Company;

use App\Models\BaseSmsTemplate;
use App\Models\Company\SmsTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Throwable;

class SmsTemplateSeeder extends Seeder
{
    public function run(): void
    {
        try {
            BaseSmsTemplate::query()->each(function (BaseSmsTemplate $baseSmsTemplate) {
                SmsTemplate::query()->updateOrCreate([
                    'id' => $baseSmsTemplate->id,
                ], [
                    'status' => $baseSmsTemplate->status,
                    'original_template' => $baseSmsTemplate->original_template,
                    'data' => $baseSmsTemplate->original_data,
                    'description' => $baseSmsTemplate->description,
                ]);
            });

            SmsTemplate::query()
                ->whereNotIn('id', BaseSmsTemplate::query()->pluck('id'))
                ->where('id', '<', 100)
                ->forceDelete();

        } catch (Throwable $e) {
            $this->command->error('Error: ' . $e->getMessage());
            Log::error($e->getMessage());
        }
    }
}
