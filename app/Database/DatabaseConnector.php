<?php

namespace App\Database;

use App\Models\Company;
use Exception;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

/**
 * Class DatabaseConnector
 */
class DatabaseConnector
{
    public const DB_CONNECTION_ADMIN = 'pgsql';

    public const DB_CONNECTION_COMPANY = 'pgsql-company';

    public const CONFIG_PREFIX = 'database.connections';

    //Database for total data from all companies

    public static function getCompaniesHostConnectionName(): string
    {
        return self::DB_CONNECTION_COMPANY;
    }

    /**
     * @param  int  $companyId
     * @return string
     */
    public static function getCompanyConnectionName(int $companyId): string
    {
        return self::DB_CONNECTION_COMPANY . '-' . $companyId;
    }

    /**
     * Switch the connection to admins db
     */
    public static function setAdminConnection(): void
    {
        DB::setDefaultConnection(self::DB_CONNECTION_ADMIN);
    }

    /**
     * Get the connection to admins db
     */
    public static function getAdminConnection(): ConnectionInterface
    {
        return DB::connection(self::DB_CONNECTION_ADMIN);
    }

    public static function getTempConnectionFor(string $db_name): string
    {
        $adminConnection = Config::get(self::CONFIG_PREFIX . '.' . self::DB_CONNECTION_ADMIN);
        $adminConnection['database'] = $db_name;
        Config::set(self::CONFIG_PREFIX . '.temp-conn-' . $db_name, $adminConnection);
        $manager = new Manager();
        $manager->addConnection($adminConnection);
        $manager->setAsGlobal();

        return 'temp-conn-' . $db_name;
    }

    public static function removeTempConnectionFor(string $db_name): void
    {
        Config::set(self::CONFIG_PREFIX . '.temp-conn-' . $db_name);
        $manager = app(Manager::class);
        $manager->setAsGlobal();
    }

    /**
     * Get the connection to admins db
     */
    public static function getCompaniesHostConnection(): ConnectionInterface
    {
        return DB::connection(self::DB_CONNECTION_COMPANY);
    }

    /**
     * Set the company connection as default
     *
     * @param  int  $companyId
     *
     * @throws Exception
     */
    public static function setCompanyConnection(int $companyId): void
    {
        if (! self::hasCompanyConnection($companyId)) {
            self::makeCompanyConnection($companyId);
        }

        DB::setDefaultConnection(self::getCompanyConnectionName($companyId));
    }

    /**
     * Get the company connection interface
     *
     * @param  int  $companyId
     * @return ConnectionInterface
     *
     * @throws Exception
     */
    public static function getCompanyConnection(int $companyId): ConnectionInterface
    {
        return self::hasCompanyConnection($companyId) ?
            DB::connection(self::getCompanyConnectionName($companyId)) :
            self::makeCompanyConnection($companyId);
    }

    /**
     * @param  int  $companyId
     * @return string
     *
     * @throws Exception
     */

    /**
     * @param  int  $companyId
     * @return ConnectionInterface
     *
     * @throws Exception
     */
    private static function makeCompanyConnection(int $companyId): ConnectionInterface
    {
        $company = Company::findOrFail($companyId);
        // Create new connection from default company host
        $companyConnectionName = self::getCompanyConnectionName($companyId);
        $companyConnection = Config::get(self::CONFIG_PREFIX . '.' . self::getCompaniesHostConnectionName());
        $companyConnection['database'] = $company->db_name;
        $companyConnection['username'] = $company->db_user;
        $companyConnection['password'] = $company->db_password;
        Config::set(self::CONFIG_PREFIX . '.' . $companyConnectionName, $companyConnection);

        // Set connection as global
        $manager = new Manager();
        $manager->addConnection($companyConnection);
        $manager->setAsGlobal();

        return DB::connection($companyConnectionName);
    }

    /**
     * @param  int  $companyId
     * @return bool
     */
    private static function hasCompanyConnection(int $companyId): bool
    {
        return Config::get(self::CONFIG_PREFIX . '.' . self::getCompanyConnectionName($companyId)) !== null;
    }
}
