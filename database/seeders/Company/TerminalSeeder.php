<?php

namespace Database\Seeders\Company;

use App\Enums\Company\TerminalTypeEnum;
use App\Models\Company\Terminal;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Throwable;

class TerminalSeeder extends Seeder
{
    public function run(): void
    {
        try {
            foreach (TerminalTypeEnum::cases() as $terminalTypeEnum) {
                /** @var Terminal|null $terminal */
                $terminal = Terminal::query()->where('type', $terminalTypeEnum)->where('branch_id', 1)->first();

                if (! $terminal) {
                    $terminal = new Terminal;
                    $terminal->branch_id = 1;
                    $terminal->type = $terminalTypeEnum;
                    $terminal->data = $terminalTypeEnum->getDataFromFeature();
                    $terminal->save();
                } else {
                    $terminal->data = $terminalTypeEnum->getDataFromFeature();
                    $terminal->save();
                }
            }
        } catch (Throwable $e) {
            $this->command->error('Error: ' . $e->getMessage());
            Log::error($e->getMessage());
        }
    }
}
