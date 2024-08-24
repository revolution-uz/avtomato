<?php

namespace App\Models\Company;

use App\Enums\SmsTemplateEnum;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * App\Models\Company\SmsTemplate
 *
 * @property-read int $id
 * @property-read string $original_template
 * @property-read string $template
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class SmsTemplate extends Model
{
    use HasFactory, TenantConnection;

    private static ?array $features = null;

    protected $fillable = [
        'id',
        'original_template',
        'template',
    ];

    public function getMessage(array $placeholders = []): string
    {
        $message = $this->template ?? $this->original_template;

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
