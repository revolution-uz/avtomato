<?php

namespace App\Console\Commands\Migrations;

use App\Database\DatabaseConnector;
use App\Database\DatabaseHelper;
use App\Models\Company;
use Illuminate\Support\Carbon;

trait CompaniesProcessTrait
{
    /**
     * @param  Company  $company
     */
    protected function showCompanyInfo(Company $company): void
    {
        $dateTime = Carbon::now()->toISOString();

        $this->info("{$dateTime} Company [id=$company->id, name=$company->name]");
    }

    public function error($string, $verbosity = null): void
    {
        $this->line("[ERROR] $string", 'error', $verbosity);
    }

    /**
     * @param  \Closure  $operation
     * @param  bool  $withoutInfo
     * @return int Result === 0 Success, Result === 1 Failure
     *
     * @throws \Exception
     */
    protected function companiesProcess(\Closure $operation, bool $withoutInfo = false): int
    {
        $companiesQuery = Company::on(DatabaseConnector::DB_CONNECTION_ADMIN);
        $companyIds = $this->option('companyIds');

        if (! $this->option('companyIds') && ! $this->option('all')) {
            $this->error('You must specify the options (--companyIds=1 or --all) to execute this command');

            return 1;
        }

        $companiesQuery = $companyIds
            ? Company::query()->whereIn('id', explode(',', $companyIds))
            : Company::withoutCreated();
        $companies = $companiesQuery->get()->all();

        if (empty($companies)) {
            $this->error('No companies found');

            return 0;
        }

        $result = 0;

        foreach ($companies as $company) {
            if (! $withoutInfo) {
                $this->showCompanyInfo($company);
            }

            $dateTime = Carbon::now()->toISOString();

            if (DatabaseHelper::isDatabaseExist($company->db_name)) {
                DatabaseConnector::setCompanyConnection($company->id);

                try {
                    if ($operation($company) === false && $result === 0) {
                        $result = 1;
                    }
                } catch (\Exception $exception) {
                    DatabaseConnector::setAdminConnection();
                    $this->error(
                        "$dateTime [id=$company->id, url=$company->urlname] " .
                        "Company command error: {$exception->getMessage()}"
                    );
                    $result = 1;
                }
            } else {
                $this->error("$dateTime [id=$company->id, url=$company->urlname] Company has no database");
                $result = 1;
            }
        }

        DatabaseConnector::setAdminConnection();

        return $result;
    }
}
