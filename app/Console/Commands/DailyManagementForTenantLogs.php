<?php

namespace App\Console\Commands;

use App\Models\Company;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Console\Command\Command as CommandAlias;
use ZipArchive;

class DailyManagementForTenantLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'daily:management-logs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Daily management of tenant logs';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        Company::all()->each(function (Company $company) {
            $logPath = storage_path("company_{$company->id}/logs/");
            $zipPath = storage_path("company_{$company->id}/zipped/");

            // Create the zipped folder if it doesn't exist
            if (! File::exists($zipPath)) {
                File::makeDirectory($zipPath, 0777, true);
            }

            $this->zipOldLogs($logPath, $zipPath);
            $this->deleteOldZippedFiles($zipPath);
        });

        return CommandAlias::SUCCESS;
    }

    /**
     * Zip logs older than 3 days and delete the original files.
     */
    private function zipOldLogs(string $logPath, string $zipPath): void
    {
        if (! File::exists($logPath)) {
            return;
        }

        foreach (File::files($logPath) as $file) {
            $lastModified = Carbon::createFromTimestamp(File::lastModified($file));

            if ($lastModified->diffInDays(Carbon::now()) >= 3) {
                $zipFileName = basename($file) . '.zip';
                $zipFullPath = $zipPath . $zipFileName;

                $zip = new ZipArchive;
                if ($zip->open($zipFullPath, ZipArchive::CREATE) === true) {
                    $zip->addFile($file, basename($file));
                    $zip->close();

                    File::delete($file);
                }
            }
        }
    }

    /**
     * Delete zipped files older than 30 days.
     */
    private function deleteOldZippedFiles(string $zipPath): void
    {
        if (! File::exists($zipPath)) {
            return;
        }

        foreach (File::files($zipPath) as $file) {
            $lastModified = Carbon::createFromTimestamp(File::lastModified($file));
            if ($lastModified->diffInDays(Carbon::now()) >= 30) {
                File::delete($file);
            }
        }
    }
}
