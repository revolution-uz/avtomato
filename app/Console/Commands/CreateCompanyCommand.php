<?php

namespace App\Console\Commands;

use App\Database\DatabaseConnector;
use App\Database\DatabaseHelper;
use App\Enums\CompanyStatus;
use App\Events\CompanyCreated;
use App\Helpers\CompanyAccountHelper;
use App\Models\Company;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

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

        foreach ($companies as $company) {
            DatabaseHelper::createCompanyDatabase($company);
            if (! $this->option('withoutMigrations')) {
                //needed for migrations correctly run
                DatabaseConnector::setCompanyConnection($company->id);
                if (Artisan::call('migrate:company', ['--companyIds' => $companyId, '--force' => true]) !== 0) {
                    throw new Exception(
                        'Migrations were executed with errors in the company ' .
                        $companyId . json_encode(Artisan::output())
                    );
                }


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

                event(new CompanyCreated($company->id));
            }
        }

        return Command::SUCCESS;
    }
}
