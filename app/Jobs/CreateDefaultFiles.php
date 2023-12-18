<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
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

        $defaultFolder = storage_path('company_default_files/app/public');
        $folderForCopying = storage_path($folderName);
        $defaultDirectories = [
            'thumbnails/avatars',
            'thumbnails/installment_documents',
            'uploads/avatars',
            'uploads/installment_documents',
            'uploads/installment_images',
        ];
        // Creating default folders
        foreach ($defaultDirectories as $directory) {
            if (! File::exists($defaultFolder . '/' . $directory)) {
                mkdir($defaultFolder . '/' . $directory, 0755, true);
            }
        }

        // Copying default files
        Storage::copy('app/public/avatar/no-image.webp', $defaultFolder . '/uploads/avatars/' . Storage::path('app/public/avatar/no-image.webp'));

        // Copy default folder to new company's folder
        if (! File::exists($folderForCopying)) {
            mkdir($folderForCopying, 0755, true);
            File::copyDirectory(storage_path('company_default_files'), $folderForCopying);
        }
    }
}
