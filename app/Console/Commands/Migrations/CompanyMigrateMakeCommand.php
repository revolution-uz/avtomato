<?php

namespace App\Console\Commands\Migrations;

use Illuminate\Database\Console\Migrations\MigrateMakeCommand;

class CompanyMigrateMakeCommand extends MigrateMakeCommand
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'make:migration:company {name : The name of the migration}
        {--create= : The table to be created}
        {--table= : The table to migrate}
        {--path= : The location where the migration file should be created}
        {--realpath : Indicate any provided migration file paths are pre-resolved absolute paths}
        {--fullpath : Output the full path of the migration}';

    public function __construct()
    {
        parent::__construct(app('migration.creator'), app('composer'));
    }

    /**
     * Get the path to the migration directory.
     *
     * @return string
     */
    protected function getMigrationPath(): string
    {
        if (! is_null($targetPath = $this->input->getOption('path'))) {
            return ! $this->usingRealPath()
                ? $this->laravel->basePath() . '/' . $targetPath
                : $targetPath;
        }

        return $this->laravel->databasePath('migrations/company');
    }
}
