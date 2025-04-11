<?php

namespace App\Models\Company\LoyaltyProgram;

use App\Models\Company\OrderBySortable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * @property int $id
 * @property string $title_uz
 * @property string $title_ru
 * @property string $body_uz
 * @property string $body_ru
 * @property bool $is_sent
 * @property int|null $image_uz_id
 * @property int|null $image_ru_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property LoyaltyProgramImage|null $image
 * @property LoyaltyProgramImage|null $imageUz
 * @property LoyaltyProgramImage|null $imageRu
 */
class Notification extends Model
{
    use HasFactory, OrderBySortable, TenantConnection;

    public static array $sortables = ['id', 'title_uz', 'title_ru', 'body_uz', 'body_ru', 'is_sent', 'created_at', 'updated_at'];

    protected $table = 'loyalty_program_notifications';

    protected $fillable = [
        'title_uz',
        'title_ru',
        'body_uz',
        'body_ru',
        'is_sent',
        'image_uz_id',
        'image_ru_id',
    ];

    public function getImageAttribute()
    {
        return $this->{'image_' . app()->getLocale()} ?? $this->imageRu;
    }

    public function imageUz(): BelongsTo
    {
        return $this->belongsTo(LoyaltyProgramImage::class, 'image_uz_id');
    }

    public function imageRu(): BelongsTo
    {
        return $this->belongsTo(LoyaltyProgramImage::class, 'image_ru_id');
    }
}
