<?php

namespace App\Models\Company\LoyaltyProgram;

use App\Models\Company\OrderBySortable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\HasApiTokens;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * @property int $id
 * @property string $name_uz
 * @property string $name_ru
 * @property int $amount
 * @property int $free_months
 * @property int $subscription_months
 * @property bool $is_free_for_previous_subscribers
 * @property int $limit_amount
 * @property bool $is_active
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 */
class Tariff extends Model
{
    use HasApiTokens, HasFactory, OrderBySortable, SoftDeletes, TenantConnection;

    public static array $sortables = ['name_uz', 'name_ru', 'amount'];

    public $table = 'loyalty_program_tariffs';

    protected $fillable = [
        'name_uz',
        'name_ru',
        'amount',
        'free_months',
        'subscription_months',
        'is_free_for_previous_subscribers',
        'limit_amount',
        'is_active',
    ];
}
