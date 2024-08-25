<?php

namespace App\Models;

use App\Enums\SmsTemplateEnum;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

/**
 * @property int $id
 * @property int $original_template
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property mixed $description
 */
class BaseSmsTemplate extends Model
{
    use CentralConnection;

    protected $fillable = [
        'id',
        'original_template',
    ];

    public function getMessage(array $placeholders = []): string
    {
        $message = $this->original_template;

        foreach ($placeholders as $placeholder => $value) {
            $message = str_replace(":{$placeholder}", $value, $message);
        }

        return $message;
    }

    public function description(): Attribute
    {
        return Attribute::make(get: fn() => SmsTemplateEnum::tryFrom($this->id)->getDefaultDescription());
    }
}
