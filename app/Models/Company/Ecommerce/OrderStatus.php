<?php

namespace App\Models\Company\Ecommerce;

use App\Models\Company\OrderBySortable;
use App\Traits\HasTranslations;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $name
 * @property string $name_uz
 * @property string $name_ru
 * @property string $name_en
 * @property string $name_oz
 * @property bool $is_active
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 */
class OrderStatus extends Model
{
    use HasFactory, HasTranslations, OrderBySortable, SoftDeletes;

    protected static array $sortables = ['id', 'name_uz', 'name_ru', 'name_en', 'name_oz', 'created_at', 'updated_at'];

    const int CREATED = 1;
    const int CANCELLED = 2;
    const int FINISHED = 3;

    protected $table = 'ecommerce_order_statuses';

    protected $fillable = [
        'id',
        'name_uz',
        'name_ru',
        'name_en',
        'name_oz',
        'is_active',
    ];

    protected $translatedColumns = ['name'];
}
