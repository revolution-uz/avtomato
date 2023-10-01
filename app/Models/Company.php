<?php

namespace App\Models;

use App\Enums\Company\CompanyStatusEnum;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\CentralConnection;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Models\Tenant;

/**
 * @property int $id
 * @property string $name
 * @property string $owner_phone
 * @property string $owner_name
 * @property CompanyStatusEnum $status
 */
class Company extends Tenant implements TenantWithDatabase
{
    use CentralConnection, HasDatabase, HasDomains;

    protected $table = 'companies';

    protected $fillable = [
        'name',
        'status',
        'owner_name',
        'owner_phone',
    ];

    protected $casts = [
        'status' => CompanyStatusEnum::class,
    ];

    public static function getCustomColumns(): array
    {
        return [
            'id',
            'name',
            'status',
            'owner_name',
            'owner_phone',
        ];
    }

    /**
     * By default, the migration uses string for the id column,and the model generates UUIDs
     * when you don't supply an id during tenant creation.
     */
    public function getIncrementing(): bool
    {
        return true;
    }
}
