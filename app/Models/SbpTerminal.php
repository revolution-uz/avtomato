<?php

namespace App\Models;

use App\Clients\QrPayClient;
use App\Services\QrPayService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

/**
 * SbpTransaction model
 *
 * @property int $id
 * @property int $company_id
 * @property int $terminal_id
 * @property string $account
 * @property string $filial_code
 * @property string $inn
 * @property string $name
 * @property int $status
 */
class SbpTerminal extends Model
{
    use CentralConnection, HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'terminal_id',
        'account',
        'filial_code',
        'inn',
        'name',
        'status',
    ];

    protected static function boot()
    {
        parent::boot();

        self::created(function (self $terminal) {
            $service = new QrPayService(new QrPayClient);
            $service->addTerminal($terminal);
        });

        self::updated(function (self $terminal) {
            $service = new QrPayService(new QrPayClient);
            $service->updateTerminal($terminal);
        });
    }
}
