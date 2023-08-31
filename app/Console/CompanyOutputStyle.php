<?php

namespace App\Console;

use App\Models\Company;
use Carbon\Carbon;
use Illuminate\Console\OutputStyle;

class CompanyOutputStyle extends OutputStyle
{
    private Company $company;

    public function setCompany(Company $company)
    {
        $this->company = $company;
    }

    public function writeln(iterable|string $messages, int $type = self::OUTPUT_NORMAL)
    {
        $dateTime = Carbon::now()->toISOString();

        if (! is_iterable($messages)) {
            $messages = [$messages];
        }

        foreach ($messages as $message) {
            if (isset($this->company)) {
                parent::writeln("$dateTime [id={$this->company->id}, name={$this->company->name}] $message", $type);
            } else {
                parent::writeln("$dateTime $message", $type);
            }
        }
    }
}
