<?php

namespace App\Models\Company\Ecommerce;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $name_uz
 * @property string $name_ru
 * @property string $name_en
 * @property string $name_oz
 * @property bool $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 */
class OrderStatus extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'e_commerce_order_statuses';
    protected $fillable = [
        'name_uz',
        'name_ru',
        'name_en',
        'name_oz',
        'is_active',
    ];
}
