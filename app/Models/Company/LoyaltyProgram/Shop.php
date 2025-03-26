<?php

namespace App\Models\Company\LoyaltyProgram;

use App\Enums\Company\LoyaltyProgramm\ShopStatusEnum;
use App\Models\Company\Attachment;
use App\Models\Company\OrderBySortable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * Class Shop
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $address
 * @property float|null $lat
 * @property float|null $lng
 * @property ShopStatusEnum $status
 * @property string $external_code
 * @property string $external_name
 * @property string $external_address
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 * @property Attachment|null $image
 */
class Shop extends Model
{
    use HasFactory, OrderBySortable, SoftDeletes, TenantConnection;

    protected static array $sortables = ['id', 'name', 'address', 'external_code', 'external_name', 'external_address', 'status'];

    protected $table = 'loyalty_program_shops';

    protected $fillable = [
        'name',
        'address',
        'lat',
        'lng',
        'status',
        'external_code',
        'external_name',
        'external_address',
    ];

    protected $casts = [
        'status' => ShopStatusEnum::class,
    ];

    public function image(): MorphOne
    {
        return $this->morphOne(Attachment::class, 'attachmentable')->latestOfMany();
    }
}
