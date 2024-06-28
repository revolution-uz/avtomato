<?php

namespace App\Services\Scoring;

use App\Clients\OilaKreditClient;
use App\Clients\OnlineMahallaClient;
use App\DTOs\PassportDataDTO;
use App\Exceptions\PassportDataNotFoundException;
use Carbon\Carbon;
use Throwable;

class PassportInfoService
{
    public function __construct(
        private readonly OilaKreditClient $oilaKreditClient,
        private readonly OnlineMahallaClient $onlineMahallaClient,
    ) {}

    public function getPassportData(string $passportSeries, string $passportNumber, Carbon $birthData): ?PassportDataDTO
    {
        try {
            return $this->oilaKreditClient->getDataByPassport($passportSeries, $passportNumber, $birthData);
        } catch (PassportDataNotFoundException|Throwable) {
        }

        try {
            return $this->onlineMahallaClient->getDataByPassport($passportSeries, $passportNumber, $birthData);
        } catch (PassportDataNotFoundException) {
        }

        return null;
    }
}
