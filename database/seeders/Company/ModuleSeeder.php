<?php

namespace Database\Seeders\Company;

use App\Models\BaseModule;
use App\Models\Company\Module;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Throwable;

class ModuleSeeder extends Seeder
{
    public function run(): void
    {
        try {
            /** @var BaseModule $baseModule */
            foreach (BaseModule::query()->get()->all() as $baseModule) {
                /** @var Module|null $module */
                $module = Module::query()->find($baseModule->id);

                if ($module) {
                    $module->update([
                        'name' => $baseModule->name,
                        'original_amount' => $baseModule->original_amount,
                    ]);
                } else {
                    Module::query()->create([
                        'id' => $baseModule->id,
                        'is_active' => $baseModule->is_default_active,
                        'name' => $baseModule->name,
                        'original_amount' => $baseModule->original_amount,
                    ]);
                }
            }

            Module::query()->whereNotIn('id', BaseModule::query()->pluck('id'))->forceDelete();

        } catch (Throwable $e) {
            $this->command->error('Error: ' . $e->getMessage());
            Log::error($e->getMessage());
        }
    }
}
