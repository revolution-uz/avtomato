<?php

namespace App\Models\Company;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * SbpTransaction model
 *
 * @property int $id
 * @property string $cheque_ext_id
 * @property float $cheque_amount
 * @property float $cheque_currency
 * @property string $cheque_partner
 * @property string $cheque_terminal_id
 * @property string $cheque_merchant_id
 * @property string $cheque_redirect_url
 * @property string $cheque_purpose_code
 * @property string $cheque_purpose
 * @property string $cheque_created_at
 * @property string $cheque_updated_at
 * @property string $exchange_rate
 * @property string $qr_amount
 * @property string $qr_currency
 * @property string $qr_qrc_id
 * @property string $qr_local_qrc_id
 * @property string $qr_locked
 * @property string $qr_pay_status
 * @property string $qr_pay_trx_id
 * @property string $callback_trx_id
 * @property string $callback_trx_time
 * @property string $callback_snd_pam
 * @property string $callback_snd_phone_masked
 * @property string $callback_add
 * @property Carbon $callback_at
 */
class SbpTransaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'cheque_ext_id',
        'cheque_amount',
        'cheque_currency',
        'cheque_partner',
        'cheque_terminal_id',
        'cheque_merchant_id',
        'cheque_redirect_url',
        'cheque_purpose_code',
        'cheque_purpose',
        'cheque_created_at',
        'cheque_updated_at',
        'exchange_rate',
        'qr_amount',
        'qr_currency',
        'qr_qrc_id',
        'qr_local_qrc_id',
        'qr_locked',
        'qr_pay_status',
        'qr_pay_trx_id',
        'callback_trx_id',
        'callback_trx_time',
        'callback_snd_pam',
        'callback_snd_phone_masked',
        'callback_add',
        'callback_at',
    ];

    protected $casts = [
        'cheque_created_at' => 'datetime',
        'cheque_updated_at' => 'datetime',
        'callback_trx_time' => 'datetime',
        'deleted_at' => 'datetime',
    ];
}
