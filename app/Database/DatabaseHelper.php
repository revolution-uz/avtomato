<?php

namespace App\Database;

use App\Models\Company;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DatabaseHelper
{
    private static function getPrefix()
    {
        $db_prefix = '';
        if (app()->runningUnitTests()) {
            $db_prefix = 'test_';
        }
        return $db_prefix;
    }

    public static function getCompanyDatabaseName(Company $company): string
    {
        return self::getPrefix() . 'company_' . $company->id;
    }

    public static function getCompanyDatabaseUser(Company $company): string
    {
        return self::getPrefix() . 'user_' . $company->id;
    }

    public static function createCompanyDatabase(Company $company): Company
    {
        $company->db_name = self::getCompanyDatabaseName($company);
        $company->db_user = self::getCompanyDatabaseUser($company);
        $company->db_password = Str::random();
        $company->save();

        $result = DB::statement('
            DO
            $do$
            BEGIN
               IF NOT EXISTS (
                  SELECT
                  FROM   pg_catalog.pg_roles
                  WHERE  rolname = \'' . $company->db_user . '\')
               THEN
                  CREATE ROLE ' . $company->db_user . ' LOGIN PASSWORD \'' . $company->db_password . '\';
               ELSE
                  ALTER USER ' . $company->db_user . ' WITH PASSWORD \'' . $company->db_password . '\';
               END IF;
            END
            $do$');

        if (!$result) {
            throw new Exception('Can not create a database user');
        }

        $result = DB::statement("CREATE DATABASE $company->db_name");
        if (!$result) {
            throw new Exception('Can not create a database user');
        }

        $config = DatabaseConnector::getTempConnectionFor($company->db_name);
        $result = DB::connection($config)->statement("GRANT ALL PRIVILEGES ON DATABASE $company->db_name TO $company->db_user");
        if (!$result) {
            throw new Exception('Can not create a database user');
        }
        $result = DB::connection($config)->statement("GRANT ALL ON schema public TO $company->db_user");
        if (!$result) {
            throw new Exception('Can not create a database user');
        }
        DatabaseConnector::removeTempConnectionFor($company->db_name);

        return $company;
    }

    public static function isDatabaseExist(mixed $db_name): bool
    {
        return !empty(DB::select("SELECT 1 FROM pg_database WHERE datname='$db_name'"));
    }
}
