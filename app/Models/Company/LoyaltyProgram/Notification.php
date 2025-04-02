<?php

namespace App\Models\Company\LoyaltyProgram;

use App\Models\Company\Attachment;
use App\Models\Company\OrderBySortable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Carbon;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * @property int $id
 * @property string $title_uz
 * @property string $title_ru
 * @property string $body_uz
 * @property string $body_ru
 * @property bool $is_sent
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Attachment|null $image
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
    ];

    public function image(): MorphOne
    {
        return $this->morphOne(Attachment::class, 'attachmentable')->latestOfMany();
    }
}
