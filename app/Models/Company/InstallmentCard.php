<?php

namespace App\Models\Company;

use App\Traits\LogsActivityDirtyOnlyTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * @property int $installment_id
 * @property Installment $installment
 * @property int $card_id
 * @property Card $card
 * @property string $deleted_at
 */
class InstallmentCard extends Model
{
    use HasFactory, LogsActivityDirtyOnlyTrait, SoftDeletes, TenantConnection;

    public $timestamps = false;

    protected $fillable = [
        'installment_id',
        'card_id',
    ];

    public function installment(): BelongsTo
    {
        return $this->belongsTo(Installment::class)->withTrashed();
    }

    public function card(): BelongsTo
    {
        return $this->belongsTo(Card::class);
    }
}
