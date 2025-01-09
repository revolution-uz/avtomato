<?php

namespace App\Models;

use App\Enums\TelegramChannelTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

/**
 * @property int $id
 * @property string $name
 * @property string $chat_id
 * @property bool $is_active
 * @property TelegramChannelTypeEnum $type
 * @property int|null $company_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class TelegramChannel extends Model
{
    use CentralConnection, HasFactory, SoftDeletes;

    protected $table = 'telegram_channels';

    protected $fillable = [
        'name',
        'chat_id',
        'is_active',
        'type',
        'company_id',
    ];

    protected $casts = [
        'type' => TelegramChannelTypeEnum::class,
    ];
}
