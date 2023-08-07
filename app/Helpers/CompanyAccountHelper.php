<?php

namespace App\Helpers;

use App\Database\DatabaseConnector;
use App\Enums\Company\UserStatus;
use App\Models\Company;
use App\Models\Company\User;
use Cache;

class CompanyAccountHelper
{
    /**
     * @param int $companyId
     * @return void
     * @throws \Exception
     */
    public static function createCompanyUser(int $companyId)
    {
        $company = Company::findOrFail($companyId);
        $companyConnection = DatabaseConnector::getCompanyConnection($companyId);
        $owner = $companyConnection->table('users')->where('phone', '=', $company->owner_phone)->first();
        if ($owner === null) {
            $director_name = explode(' ', $company->owner_name);
            User::on(DatabaseConnector::getCompanyConnectionName($companyId))->create(
                [
                    'phone' => $company->owner_phone,
                    'first_name' => $director_name[0] ?? null,
                    'last_name' => $director_name[1] ?? null,
                    'status' => UserStatus::ACTIVE
                ]
            );
        }
    }
}
