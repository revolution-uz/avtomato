<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $value
 * @property int $type
 * @property string $description
 */
class Settings extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'value',
        'type',
        'description',
    ];
}