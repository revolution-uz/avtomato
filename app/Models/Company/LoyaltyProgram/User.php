<?php

namespace App\Models\Company\LoyaltyProgram;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $phone
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string|null $avatar
 * @property Carbon|null $birth_date
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 */
class User extends Model
{
    use HasFactory;

    public $table = 'loyalty_program_users';

    protected $fillable = ['phone', 'first_name', 'last_name', 'avatar', 'birth_date'];

    protected $casts = [
        'birth_date' => 'date',
    ];
}
