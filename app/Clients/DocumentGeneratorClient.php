<?php

namespace App\Clients;

use App\Enums\ClientCodeEnum;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\TemplateProcessor;

class DocumentGeneratorClient extends BaseClient
{
    public function __construct()
    {
        $this->channel = 'document-generator-client';
    }

    public function getClientCode(): ClientCodeEnum
    {
        return ClientCodeEnum::DOCUMENT_GENERATOR;
    }

    public function generate(string $document_name, string $document_template, array $data)
    {
        $templateProcessor = new TemplateProcessor(storage_path('../document_files/templates/' . $document_template));
        foreach ($data as $search => $value) {
            $templateProcessor->setValue($search, htmlspecialchars($value));
        }
        $fileName = 'documents/' . $document_name;
        $templateProcessor->saveAs(storage_path('app/public/' . $fileName));
        $filePath = Storage::disk('local')->path($document_name);
        $templateProcessor->saveAs($filePath);

        return $fileName;
    }
}
