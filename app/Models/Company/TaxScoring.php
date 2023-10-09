<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

class TaxScoring extends Model
{
    use HasFactory;
    use TenantConnection;

    protected $dateFormat = 'U';

    protected $fillable = [
        'customer_id',
        'amount',
        'ball',
        'description',
    ];
}
