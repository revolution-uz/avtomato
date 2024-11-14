<?php

namespace App\Models\Company\Ecommerce;

use App\Models\Company\OrderBySortable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $address_id
 * @property string $name_uz
 * @property string $name_en
 * @property string $name_ru
 * @property string $name_oz
 * @property string $description_uz
 * @property string $description_ru
 * @property string $description_en
 * @property string $description_oz
 * @property string $is_prepaid
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 */
class DeliveryPoint extends Model
{
    use HasFactory, OrderBySortable, SoftDeletes;
    protected static array $sortables = ['id', 'name_uz', 'name_ru', 'name_en', 'name_oz', 'description_uz', 'description_ru', 'description_en', 'description_oz', 'is_prepaid'];

    protected $table = 'ecommerce_delivery_points';
    protected $fillable = [
        'address_id',
        'name_uz',
        'name_ru',
        'name_en',
        'name_oz',
        'description_uz',
        'description_ru',
        'description_en',
        'description_oz',
        'is_prepaid',
    ];
}
