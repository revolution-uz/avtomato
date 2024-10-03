<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

/**
 * @property int $id
 * @property int $original_template
 * @property array|null $original_data
 * @property string|null $description
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class BaseSmsTemplate extends Model
{
    use CentralConnection;

    protected $fillable = [
        'id',
        'original_template',
        'original_data',
        'description',
    ];

    protected $casts = [
        'original_data' => 'array',
    ];

    public function getMessage(array $placeholders = []): string
    {
        $message = $this->original_template;

        foreach ($placeholders as $placeholder => $value) {
            $message = str_replace(":{$placeholder}", $value, $message);
        }

        return $message;
    }
}
