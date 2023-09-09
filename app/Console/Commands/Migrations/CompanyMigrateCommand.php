<?php

namespace App\Console\Commands\Migrations;

use App\Console\CompanyOutputStyle;
use App\Database\DatabaseConnector;
use Database\Seeders\Company\CompanySeeder;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Console\Migrations\MigrateCommand;

class CompanyMigrateCommand extends MigrateCommand
{
    use CompanyMigrateTrait;
    use CompaniesProcessTrait;

    /**
    /**
     *
     * @var CompanyOutputStyle
     */
    public $output;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:company {--database= : The database connection to use}
                {--force : Force the operation to run when in production}
                {--path=* : The path(s) to the migrations files to be executed}
                {--realpath : Indicate any provided migration file paths are pre-resolved absolute paths}
                {--schema-path= : The path to a schema dump file}
                {--pretend : Dump the SQL queries that would be run}
                {--seed : Indicates if the seed task should be re-run}
                {--step : Force the migrations to be run so they can be rolled back individually}
                {--companyIds= : Companies to apply command}
                {--all : Run for all companies}';

    public function __construct()
    {
        parent::__construct(app('migrator'), app(Dispatcher::class));
    }

    /**
     * Execute the console command.
     *
     * @return int
     *
     * @throws \Exception
     */
    public function handle(): int
    {
        if (! $this->confirmToProceed()) {
            return 1;
        }
        $output = new CompanyOutputStyle($this->input, $this->output);

        return $this->companiesProcess(
            function ($company) use ($output) {
                $this->migrator->usingConnection(
                    DatabaseConnector::getCompanyConnectionName($company->id),
                    function () use ($company, $output) {
                        $this->prepareDatabase();
                        $output->setCompany($company);

                        $this->migrator->setOutput($output);
                        $this->migrator
                            ->run(
                                $this->getMigrationPath(), [
                                    'pretend' => $this->option('pretend'),
                                    'step' => $this->option('step'),
                                ]
                            );

                        if ($this->option('seed') && !$this->option('pretend')) {
                            $this->call('db:seed', ['--force' => true, '--class' => CompanySeeder::class]);
                        }
                    }
                );
            }
        );
    }
}
