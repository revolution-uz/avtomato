<?php

namespace App\Console\Commands;

use App\Database\DatabaseConnector;
use App\Database\DatabaseHelper;
use App\Enums\Company\CompanyStatus;
use App\Helpers\CompanyAccountHelper;
use App\Models\Company;
use Database\Seeders\Company\PermissionSeeder;
use DB;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class CreateCompanyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'company:create
                            {company : The ID of the company}
                            {--withoutMigrations= : If the command does not run migrations}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command creates a new company';

    /**
     * Execute the console command.
     *
     * @return int
     *
     * @throws Exception
     */
    public function handle(): int
    {
        $companyId = $this->argument('company');
        if ((string) $companyId === '-1') {
            $companies = Company::whereStatus(CompanyStatus::NEW)->get();
        } else {
            $companies = [Company::findOrFail($companyId)];
        }
        $cnt = count($companies);
        $this->info(strtr("Creating :database for :companies_count :companies_str", [
            ':database' => $cnt > 1 ? 'databases' : 'database',
            ':companies_count' => $cnt,
            ':companies_str' => $cnt > 1 ? 'companies' : 'company'
        ]));
        foreach ($companies as $company) {
            $query = "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME =  ?";
            $db = DB::select($query, [DatabaseHelper::getCompanyDatabaseName($company)]);
            if (empty($db)) {
                DatabaseHelper::createCompanyDatabase($company);
            } else {
                $this->info("[$company->name] Skipping database creation, it already exist.");
            }

            $this->info("[$company->name] Database created.");
            if (! $this->option('withoutMigrations')) {
                //needed for migrations correctly run
                DatabaseConnector::setCompanyConnection($company->id);
                $this->info("[$company->name] Running migrations");
                if (Artisan::call('migrate:company', ['--companyIds' => $companyId, '--force' => true]) !== 0) {
                    throw new Exception(
                        'Migrations were executed with errors in the company ' .
                        $companyId . json_encode(Artisan::output())
                    );
                }

                $this->info("[$company->name] Creating admin user");
                CompanyAccountHelper::createCompanyUser($companyId);
                $company->update([
                    'status' => CompanyStatus::CREATED,
                ]);

                DatabaseConnector::setCompanyConnection($company->id);
                if (Artisan::call('db:seed', ['--class' => PermissionSeeder::class, '--force' => true]) !== 0) {
                    throw new Exception(
                        'Permission seeder was executed with errors in the company ' .
                        $companyId . json_encode(Artisan::output())
                    );
                }

            } else {
                $this->info("[$company->name] Migrations skipped");
            }
        }
        $this->info("[$company->name] All done.");
        return Command::SUCCESS;
    }
}
