<?php

namespace App\Models\Company;

use App\Enums\Company\InstallmentAttachmentTypeEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * @property int $id
 * @property int $user_id
 * @property int $installment_id
 * @property string $file_path
 * @property InstallmentAttachmentTypeEnum $type
 * @property User $user
 * @property Installment $installment
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
class InstallmentAttachment extends Model
{
    use HasFactory;
    use LogsActivity;
    use SoftDeletes;
    use TenantConnection;

    protected $fillable = [
        'user_id',
        'installment_id',
        'file_path',
        'type',
    ];

    protected $casts = [
        'type' => InstallmentAttachmentTypeEnum::class,
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable();
    }

    public function installment()
    {
        return $this->belongsTo(Installment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
