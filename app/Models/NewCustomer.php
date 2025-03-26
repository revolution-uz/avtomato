<?php

namespace App\Models;

use App\Events\TelegramEvents\NewCustomerTelegramMessageEvent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

/**
 * @property int $id
 * @property string $full_name
 * @property string $phone
 * @property bool $is_checked
 * @property string $comment
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class NewCustomer extends Model
{
    use CentralConnection, HasFactory;

    protected $fillable = [
        'full_name',
        'phone',
        'is_checked',
        'comment',
        'created_at',
        'updated_at',
    ];

    protected static function boot()
    {
        parent::boot();

        self::created(function (self $model) {
            event(new NewCustomerTelegramMessageEvent($model));
        });
    }
}
