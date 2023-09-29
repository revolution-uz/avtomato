<?php

namespace App\Models\Company;

use App\Enums\Company\PhoneTypeEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property PhoneTypeEnum $type
 * @property string $comment
 * @property int $phone_id
 * @property int $phoneable_id
 * @property string $phoneable_type
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $deleted_at
 *
 * @property Phone $phone
 */
class Phoneable extends Model
{
    protected $dateFormat = 'U';

    protected $fillable = [
        'type',
        'comment',
        'phone_id',
        'phoneable_id',
        'phoneable_type',
    ];

    protected $casts = [
        'type' => PhoneTypeEnum::class,
    ];

    public function phone(): BelongsTo
    {
        return $this->belongsTo(Phone::class);
    }
}
