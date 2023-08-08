<?php

namespace App\Console\Commands\Migrations;

use Illuminate\Database\Console\Migrations\MigrateCommand;

trait CompanyMigrateTrait
{
    /**
     * Get the path to the migration directory.
     *
     * @return string
     */
    protected function getMigrationPath(): string
    {
        /**
         * @var MigrateCommand $this
         */
        return $this->laravel->databasePath('migrations/company');
    }
}
