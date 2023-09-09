<?php

namespace App\Console\Commands;

use App\Database\DatabaseConnector;
use App\Database\DatabaseHelper;
use App\Enums\Company\CompanyStatus;
use App\Models\Company;
use Database\Seeders\Company\CompanySeeder;
use Database\Seeders\Company\FeatureSeeder;
use Database\Seeders\Company\PermissionSeeder;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Command\Command as CommandAlias;

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
        /** @var Company $company */
        $company = Company::query()
            ->where('status', CompanyStatus::CREATED)
            ->where('id', $companyId)
            ->firstOrFail();

        $this->info(strtr("Creating database for :company_name", [':company_name' => $company->name]));
        $query = "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME =  ?";
        $db = DB::select($query, [DatabaseHelper::getCompanyDatabaseName($company)]);

        if (empty($db)) {
            DatabaseHelper::createCompanyDatabase($company);
        } else {
            $this->info("[$company->name] Skipping database creation, it already exist.");
        }

        $this->info("[$company->name] Database created.");

        if (!$this->option('withoutMigrations')) {
            // needed for migrations correctly run
            DatabaseConnector::setCompanyConnection($company->id);
            $this->info("[$company->name] Running migrations");

            if (Artisan::call('migrate:company', ['--companyIds' => $companyId, '--force' => true]) !== 0) {
                throw new Exception(
                    'Migrations were executed with errors in the company ' .
                    $companyId . json_encode(Artisan::output())
                );
            }

            $company->update([
                'status' => CompanyStatus::ACTIVE,
            ]);

            DatabaseConnector::setCompanyConnection($company->id);

            if (Artisan::call('db:seed', ['--class' => CompanySeeder::class, '--force' => true]) !== 0) {
                throw new Exception(
                    'Company seeder was executed with errors in the company ' .
                    $companyId . json_encode(Artisan::output())
                );
            }

            if (Artisan::call('db:seed', ['--class' => FeatureSeeder::class, '--force' => true]) !== 0) {
                throw new Exception(
                    'Company seeder was executed with errors in the company ' .
                    $companyId . json_encode(Artisan::output())
                );
            }
        } else {
            $this->info("[$company->name] Migrations skipped");
        }

        $this->info("[$company->name] All done.");

        return CommandAlias::SUCCESS;
    }
}
