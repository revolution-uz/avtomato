<?php

namespace App\Models\Company;

use App\Traits\LogsActivityDirtyOnlyTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * @property int $id
 * @property int $admin_id
 * @property int $installment_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property string $comment
 * @property CompanyAdmin $admin
 * @property Installment $installment
 */
class InstallmentComment extends Model
{
    use HasFactory, LogsActivityDirtyOnlyTrait, SoftDeletes, TenantConnection;

    protected $fillable = [
        'admin_id',
        'installment_id',
        'comment',
    ];

    public function installment(): BelongsTo
    {
        return $this->belongsTo(Installment::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(CompanyAdmin::class);
    }
}
