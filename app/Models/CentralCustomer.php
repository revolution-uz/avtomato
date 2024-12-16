<?php

namespace App\Models;

use App\Models\Company\OrderBySortable;
use App\Traits\LogsActivityDirtyOnlyTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

/**
 * @property int $id
 * @property string|null $pinfl
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string|null $middle_name
 * @property Carbon $atmos_pinfl_searched_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 *
 * @method static Builder|static query()
 * @method static CentralCustomer find(int $id)
 * @method static CentralCustomer findOrFail(int $id)
 * @method static CentralCustomer first()
 * @method static CentralCustomer firstOrFail()
 * @method CentralCustomer saveOrFail(array $array = [])
 */
class CentralCustomer extends Model
{
    use CentralConnection, HasApiTokens, HasFactory, LogsActivityDirtyOnlyTrait, OrderBySortable, SoftDeletes;

    public static array $sortables = ['id', 'pinfl', 'first_name', 'last_name', 'birth_date'];

    protected $table = 'customers';

    protected $fillable = [
        'pinfl',
        'first_name',
        'last_name',
        'birth_date',
    ];
}
