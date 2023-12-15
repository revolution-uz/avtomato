<?php

namespace App\Listeners;

use Illuminate\Support\Facades\File;

class CompanyCreatedListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        $newCompanyName = $event->name;
        $defaultFolder = storage_path('company_default_files/app/public');
        $folderForCopying = storage_path('company_files');

        if (! File::exists($defaultFolder)) {
            mkdir($defaultFolder, 0755, true);
            mkdir($defaultFolder . '/thumbnails/avatars', 0755, true);
            mkdir($defaultFolder . '/thumbnails/installment_documents', 0755, true);
            mkdir($defaultFolder . '/uploads/avatars', 0755, true);
            mkdir($defaultFolder . '/uploads/installment_documents', 0755, true);
            mkdir($defaultFolder . '/uploads/installment_images', 0755, true);
        }
        if (! File::exists($folderForCopying)) {
            mkdir($folderForCopying, 0755, true);
        }
        $destinationFolder = $folderForCopying . '/' . $newCompanyName . '_' . uniqid();

        File::copyDirectory(storage_path('company_default_files'), $destinationFolder);
    }
}
