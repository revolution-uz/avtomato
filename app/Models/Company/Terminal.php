<?php

namespace App\Models\Company;

use App\Enums\Company\TerminalTypeEnum;
use App\Traits\LogsActivityDirtyOnlyTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * @property int $id
 * @property int $branch_id
 * @property TerminalTypeEnum $type
 * @property array|null $data
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Terminal extends Model
{
    use HasFactory, LogsActivityDirtyOnlyTrait, TenantConnection;

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
