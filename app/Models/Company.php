<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        'name',
        'db_name',
        'status',
        'subdomain',
        'db_password',
        'db_user',
        'owner_name',
        'owner_phone'
    ];
}
