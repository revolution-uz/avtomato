<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

class InstallmentGraph extends Model
{
    use HasFactory;
    use SoftDeletes;
    use TenantConnection;


    protected $dateFormat = 'U';

    protected $fillable = [
        'installment_id',
        'amount',
        'paid_amount',
        'overdue_days',
        'status',
        'starts_in',
    ];
}
