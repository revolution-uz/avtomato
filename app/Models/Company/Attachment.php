<?php

namespace App\Models\Company;

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
}
