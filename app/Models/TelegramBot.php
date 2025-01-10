<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

/**
 * @property int $id
 * @property string $username
 * @property string $token
 * @property Carbon|null $last_used_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class TelegramBot extends Model
{
    use CentralConnection, HasFactory, SoftDeletes;

    protected $fillable = [
        'username',
        'token',
        'last_used_at',
    ];

    protected $casts = [
        'token' => 'encrypted',
        'last_used_at' => 'datetime',
    ];

    public static function getNextInSequence(): self
    {
        $bot = self::query()->orderBy('last_used_at')->firstOrFail();
        $bot->touch('last_used_at');

        return $bot;
    }
}
