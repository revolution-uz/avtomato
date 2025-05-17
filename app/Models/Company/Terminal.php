<?php

namespace App\Models\Company;

use App\Enums\Company\TerminalTypeEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $branch_id
 * @property TerminalTypeEnum $type
 * @property array|null $data
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Terminal extends Model
{
    protected $fillable = [
        'branch_id',
        'type',
        'data',
    ];

    protected $casts = [
        'type' => TerminalTypeEnum::class,
        'data' => 'encrypted:json',
    ];
}
