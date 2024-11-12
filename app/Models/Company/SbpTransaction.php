<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
