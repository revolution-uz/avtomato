<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Stancl\Tenancy\Contracts\TenantWithDatabase;

class CreateDefaultFiles implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    protected TenantWithDatabase $tenant;

    public function __construct(TenantWithDatabase $tenant)
    {
        $this->tenant = $tenant;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Get the new company's database name
        $folderName = $this->tenant->database()->getName();
        $folderForCopying = storage_path($folderName);

        // Copy default folder to new company's folder
        if (! File::exists($folderForCopying)) {
            mkdir($folderForCopying, 0755, true);
            File::copyDirectory(storage_path('companies_default_files'), $folderForCopying);
        }
    }
}
