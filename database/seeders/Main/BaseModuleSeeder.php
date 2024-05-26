<?php

namespace Database\Seeders\Main;

use App\Enums\ModuleEnum;
use App\Models\BaseModule;
use Illuminate\Database\Seeder;

class BaseModuleSeeder extends Seeder
{
    public function run(): void
    {
        foreach (ModuleEnum::cases() as $case) {
            BaseModule::query()->firstOrCreate([
                'name' => $case->value,
            ], [
                'original_amount' => 0,
            ]);
        }

        BaseModule::query()->whereNotIn('name', ModuleEnum::cases())->forceDelete();
    }
}
