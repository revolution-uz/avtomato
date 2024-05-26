<?php

namespace Database\Seeders\Company;

use App\Models\BaseModule;
use App\Models\Company\Module;
use Illuminate\Database\Seeder;

class ModuleSeeder extends Seeder
{
    public function run(): void
    {
        /** @var BaseModule $baseModule */
        foreach (BaseModule::query()->get()->all() as $baseModule) {
            /** @var Module|null $module */
            $module = Module::query()->find($baseModule->id);

            if ($module) {
                $module->update([
                    'name' => $baseModule->name,
                    'description' => $baseModule->description,
                    'original_amount' => $baseModule->original_amount,
                ]);
            } else {
                Module::query()->create([
                    'id' => $baseModule->id,
                    'is_active' => $baseModule->is_default_active,
                    'name' => $baseModule->name,
                    'description' => $baseModule->description,
                    'original_amount' => $baseModule->original_amount,
                ]);
            }
        }

        Module::query()->whereNotIn('id', BaseModule::query()->pluck('id'))->forceDelete();
    }
}
