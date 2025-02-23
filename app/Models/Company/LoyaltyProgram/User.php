<?php

namespace App\Models\Company\LoyaltyProgram;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;

    public $table = 'loyalty_program_users';

    protected $fillable = ['phone', 'device_id', 'login_attempts'];

    protected $hidden = ['remember_token'];
}
