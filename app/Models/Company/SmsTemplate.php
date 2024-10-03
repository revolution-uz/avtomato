<?php

namespace App\Models\Company;

use App\Enums\SmsTemplateEnum;
use Illuminate\Database\Eloquent\Builder;
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
 * @property-read array|null $data
 * @property string|null $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder debtTemplates()
 */
class SmsTemplate extends Model
{
    use HasFactory, OrderBySortable, TenantConnection;

    public static array $sortables = ['id'];

    protected $fillable = [
        'id',
        'original_template',
        'template',
        'data',
        'description',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function getMessage(array $placeholders = []): string
    {
        $message = $this->template ?? $this->original_template;

        foreach ($placeholders as $placeholder => $value) {
            $message = str_replace(":{$placeholder}", $value, $message);
        }

        return $message;
    }

    public function getExampleMessage(): string
    {
        return $this->getMessage(SmsTemplateEnum::getExampleData());
    }

    public function scopeDebtTemplates($query)
    {
        return $query
            ->whereNotNull('data')
            ->whereJsonContains('data->is_active', true)
            ->whereJsonContains('data->category', 'debt-templates');
    }
}
