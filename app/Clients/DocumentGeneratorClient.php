<?php

namespace App\Clients;

use App\Enums\ClientCodeEnum;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\TemplateProcessor;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

    public function generateStream(string $document_name, string $document_template, array $data, ?array $tables = null): StreamedResponse
    {
        $templateProcessor = new TemplateProcessor(storage_path('../document_files/templates/' . $document_template));

        if ($tables) {
            foreach ($tables as $table) {
                $templateProcessor->cloneRow($table['identifier'], $table['row_count']);
            }
        }

        foreach ($data as $search => $value) {
            $templateProcessor->setValue($search, htmlspecialchars($value));
        }

        $fileName = 'documents/' . $document_name;
        $filePath = Storage::disk('temp')->path($fileName);
        $templateProcessor->saveAs($filePath);

        $response = Storage::disk('temp')->download($fileName);
        $response->headers->set('Access-Control-Allow-Origin', '*');

        return $response;
    }
}
