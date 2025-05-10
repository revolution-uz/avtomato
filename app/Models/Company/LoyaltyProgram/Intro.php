<?php

namespace App\Models\Company\LoyaltyProgram;

use App\Models\Company\OrderBySortable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * Class Intro
 *
 * @property int $id
 * @property int $image_path
 * @property int $order
 * @property bool $is_active
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read string $image_url
 */
class Intro extends Model
{
    use HasFactory, OrderBySortable, TenantConnection;

    protected static array $sortables = ['id', 'order', 'is_active'];

    protected $table = 'loyalty_program_intros';

    protected $fillable = [
        'image_path',
        'order',
        'is_active',
    ];

    public function getImageUrlAttribute()
    {
        return tenant_asset($this->image_path);
    }
}
