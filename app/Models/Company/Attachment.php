<?php

namespace App\Models\Company;

use App\Enums\Company\AttachmentTypeEnum;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    use HasUuids;

    protected $fillable = [
        'type',
        'path',
        'name',
        'original_name',
        'mime',
    ];

    protected $casts = [
        'type' => AttachmentTypeEnum::class,
    ];
}
