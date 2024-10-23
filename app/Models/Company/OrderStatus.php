<?php

namespace App\Models\Company;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $name_uz
 * @property string $name_ru
 * @property string $name_en
 * @property string $name_oz
 * @property int $order_stage_id
 * @property int $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 * @property-read OrderStage $orderStage
 */
class OrderStatus extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'name_uz',
        'name_ru',
        'name_en',
        'name_oz',
        'order_stage_id',
        'status',
    ];

    public function orderStage(): BelongsTo
    {
        return $this->belongsTo(OrderStage::class);
    }
}
