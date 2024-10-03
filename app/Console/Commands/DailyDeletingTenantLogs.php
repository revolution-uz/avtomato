<?php

namespace App\Console\Commands;

use App\Models\Company;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Console\Command\Command as CommandAlias;

class DailyDeletingTenantLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'daily:delete-logs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Daily delete of tenant logs';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        foreach (Company::all() as $company) {
            $logPath = storage_path("company_{$company->id}/logs/");
            if (! File::exists($logPath)) {
                continue;
            }
            foreach (File::files($logPath) as $file) {
                $lastModified = Carbon::createFromTimestamp(File::lastModified($file));
                if ($lastModified->diffInDays(Carbon::now()) >= 14) {
                    File::delete($file);
                }
            }
        }

        return CommandAlias::SUCCESS;
    }
}
