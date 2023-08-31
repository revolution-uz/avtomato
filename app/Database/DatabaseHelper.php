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

        $result = DB::statement("CREATE USER $company->db_user WITH ENCRYPTED PASSWORD '$company->db_password'");
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
