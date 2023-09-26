<?php

namespace App\Models;

use App\Database\DatabaseConnector;
use App\Enums\Company\CompanyStatusEnum;
use Database\Factories\Company\CompanyFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $owner_phone
 * @property string $owner_name
 * @property string $subdomain
 * @property string $db_user
 * @property string $db_password
 * @property CompanyStatusEnum $status
 *
 * The followings are the available model methods (scopes, relations):
 * @method static static|Builder|\Illuminate\Database\Query\Builder withoutCreated()
 */
class Company extends Model
{
    use HasFactory;

    protected $connection = DatabaseConnector::DB_CONNECTION_ADMIN;
    protected $dateFormat = 'U';

    protected $fillable = [
        'name',
        'db_name',
        'status',
        'subdomain',
        'db_password',
        'db_user',
        'owner_name',
        'owner_phone'
    ];

    protected $casts = [
        'status' => CompanyStatusEnum::class,
    ];

    protected static function newFactory(): CompanyFactory
    {
        return new CompanyFactory();
    }

    /**
     * @param Builder $query
     * @return Builder mixed
     */
    public function scopeWithoutCreated(Builder $query): Builder
    {
        return $query->where('status', '!=', CompanyStatusEnum::CREATED);
    }
}
