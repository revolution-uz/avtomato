<?php

namespace App\DTOs\Scoring\Katm\StartInfo;

use App\DTOs\Scoring\Base\BaseScoringResponse;
use App\DTOs\Scoring\Katm\StartInfo\Report\KatmStartInfoReportDTO;
use App\DTOs\Scoring\Katm\StartInfo\Report23\KatmStartInfoReport23DTO;

class KatmStartInfoResponseDTO extends BaseScoringResponse
{
    public function __construct(
        public ?KatmStartInfoReportDTO $report,
        public ?KatmStartInfoReport23DTO $report23,
    ) {}

    public static function parseFromXml(string $xml): self
    {
        $cleanedXmlContent = trim(preg_replace('/<\?xml.*?\?>/', '', $xml));
        $xmlObject = simplexml_load_string("<root>{$cleanedXmlContent}</root>");
        $array = json_decode(json_encode($xmlObject), true);
        $array = self::convertEmptyArraysToNull($array);

        return self::from($array);
    }

    private static function convertEmptyArraysToNull(mixed $array)
    {
        foreach ($array as $key => $value) {
            if (is_array($value) && empty($value)) {
                $array[$key] = null;
            } elseif (is_array($value)) {
                $array[$key] = self::convertEmptyArraysToNull($value);
            }
        }

        return $array;
    }
}
