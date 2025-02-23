<?php

namespace App\Models;

use App\Clients\UniredSbpQRPayClient;
use App\Services\QrPayService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

/**
 * SbpTransaction model
 *
 * @property int $id
 * @property int $company_id
 * @property int $terminal_id
 * @property string $account_number
 * @property string $mfo
 * @property string $inn
 * @property string $purpose_code
 * @property string $company_name
 * @property string $status
 */
class SbpAccount extends Model
{
    use CentralConnection, HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'terminal_id',
        'account_number',
        'mfo',
        'inn',
        'purpose_code',
        'company_name',
        'status',
    ];

    protected static function boot()
    {
        parent::boot();

        self::created(function (self $terminal) {
            $service = new QrPayService(new UniredSbpQRPayClient);
            $service->addAccount($terminal);
        });

        self::deleted(function (self $terminal) {
            $service = new QrPayService(new UniredSbpQRPayClient);
            $service->deleteAccount($terminal);
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
