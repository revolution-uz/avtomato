<?php

namespace App\Models;

use App\Enums\Company\CompanyStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
 * @property string $address
 * @property string $bank_name
 * @property string $bank_code
 * @property string $account_number
 * @property string $tin
 * @property CompanyStatusEnum $status
 * @property int $payment_day
 */
class Company extends Tenant implements TenantWithDatabase
{
    use CentralConnection, HasDatabase, HasDomains, HasFactory;

    const REVO_TENANT_ID = 1;

    protected $table = 'companies';

    protected $fillable = [
        'id',
        'name',
        'owner_name',
        'owner_phone',
        'address',
        'bank_name',
        'bank_code',
        'account_number',
        'tin',
        'status',
        'payment_day',
    ];

    protected $casts = [
        'status' => CompanyStatusEnum::class,
    ];

    public static function getCustomColumns(): array
    {
        return [
            'id',
            'name',
            'owner_name',
            'owner_phone',
            'address',
            'bank_name',
            'bank_code',
            'account_number',
            'tin',
            'status',
            'payment_day',
            'created_at',
            'updated_at',
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

    public function isActive(): bool
    {
        return $this->status === CompanyStatusEnum::ACTIVE;
    }
}
