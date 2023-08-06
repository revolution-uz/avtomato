<?php

namespace App\Console\Commands\Migrations;

use App\Database\DatabaseConnector;
use Illuminate\Database\Console\Migrations\RollbackCommand;
use Symfony\Component\Console\Input\InputOption;

class CompanyRollbackCommand extends RollbackCommand
{
    use CompanyMigrateTrait;
    use CompaniesProcessTrait;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'migrate:rollback:company';

    public function __construct()
    {
        parent::__construct(app('migrator'));
    }

    /**
     * Execute the console command.
     *
     * @return int
     *
     * @throws \Exception
     */
    public function handle()
    {
        if (! $this->confirmToProceed()) {
            return 1;
        }

        return $this->companiesProcess(
            function ($company) {
                $this->migrator->usingConnection(
                    DatabaseConnector::getCompanyConnectionName($company->id),
                    function () {
                        $this->migrator->setOutput($this->output)->rollback(
                            $this->getMigrationPaths(),
                            [
                                'pretend' => $this->option('pretend'),
                                'step' => (int) $this->option('step'),
                            ]
                        );
                    }
                );
            }, true
        );
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions(): array
    {
        return [
            ['all', null, InputOption::VALUE_NONE, 'Run for all companies'],
            ['companyIds', null, InputOption::VALUE_OPTIONAL, 'The companies to apply command'],
            ['force', null, InputOption::VALUE_NONE, 'Force the operation to run when in production'],
            [
                'path',
                null,
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'The path(s) to the migrations files to be executed',
            ],
            [
                'realpath',
                null,
                InputOption::VALUE_NONE,
                'Indicate any provided migration file paths are pre-resolved absolute paths',
            ],
            ['pretend', null, InputOption::VALUE_NONE, 'Dump the SQL queries that would be run'],
            ['step', null, InputOption::VALUE_OPTIONAL, 'The number of migrations to be reverted'],
        ];
    }
}
