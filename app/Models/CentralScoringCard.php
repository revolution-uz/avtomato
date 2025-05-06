<?php

namespace App\Models;

use App\Traits\LogsActivityDirtyOnlyTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

/**
 * @property int $id
 * @property string $number
 * @property string $expiry
 * @property bool $is_verified
 * @property string|null $external_id
 *
 * @method self whereNumber(string $cardNumber)
 * @method self verified()
 */
class CentralScoringCard extends Model
{
    use CentralConnection, HasFactory, LogsActivityDirtyOnlyTrait;

    protected $fillable = [
        'number',
        'expiry',
        'is_verified',
        'external_id',
        'hash',
    ];

    protected $casts = [
        'number' => 'encrypted',
    ];

    protected $hidden = [
        'hash',
    ];

    public static function hashing(string $value, int $prime = 1000000007, int $base = 37): int
    {
        $hashValue = 0;
        for ($i = 0; $i < strlen($value); $i++) {
            $hashValue = ($hashValue * $base + ord($value[$i])) % $prime;
        }

        return $hashValue;
    }

    public function hash(): Attribute
    {
        return Attribute::make(set: fn ($value) => self::hashing($value));
    }

    public function computingBeforeInsert(): void
    {
        $this->is_verified = false;
        $this->hash = (int) $this->number;
    }

    public function scopeWhereNumber($query, $cardNumber)
    {
        return $query->where('hash', self::hashing($cardNumber));
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }
}
