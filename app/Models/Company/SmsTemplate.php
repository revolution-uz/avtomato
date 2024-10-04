<?php

namespace App\Models\Company;

use App\Enums\Company\SmsTemplateEnum;
use App\Enums\Company\SmsTemplateStatusEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * App\Models\Company\SmsTemplate
 *
 * @property-read int $id
 * @property SmsTemplateStatusEnum $status
 * @property string $original_template
 * @property string $template
 * @property array|null $data
 * @property string|null $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder debtTemplates()
 */
class SmsTemplate extends Model
{
    use HasFactory, OrderBySortable, SoftDeletes, TenantConnection;

    public static array $sortables = ['id'];

    protected $fillable = [
        'id',
        'status',
        'original_template',
        'template',
        'data',
        'description',
    ];

    protected $casts = [
        'status' => SmsTemplateStatusEnum::class,
        'data' => 'array',
    ];

    protected $attributes = [
        'status' => SmsTemplateStatusEnum::DRAFT,
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
            ->where('status', SmsTemplateStatusEnum::ACTIVE)
            ->whereJsonContains('data->category', 'debt-templates');
    }
}
