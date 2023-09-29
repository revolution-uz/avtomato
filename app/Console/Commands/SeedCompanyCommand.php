<?php

namespace App\Console\Commands;

use App\Console\Commands\Migrations\CompaniesProcessTrait;
use App\Console\CompanyOutputStyle;
use App\Database\DatabaseConnector;
use Database\Seeders\Company\CompanySeeder;
use Exception;
use Illuminate\Database\Console\Seeds\SeedCommand;

class SeedCompanyCommand extends SeedCommand
{
    use CompaniesProcessTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'company:seed
                            {--companyIds= : Companies to apply command}
                            {--all : Run for all companies}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command seeds a data for companies';

    /**
     * Execute the console command.
     *
     * @return int
     *
     * @throws Exception
     */
    public function handle(): int
    {
        if (!$this->confirmToProceed()) {
            return 1;
        }
        $output = new CompanyOutputStyle($this->input, $this->output);

        return $this->companiesProcess(
            function ($company) use ($output) {
                DatabaseConnector::setCompanyConnection($company->id);
                $this->setOutput($output);
                $this->call(CompanySeeder::class);
            }
        );
    }
}
