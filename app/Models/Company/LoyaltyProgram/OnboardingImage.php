<?php

namespace App\Models\Company\LoyaltyProgram;

use App\Models\Company\OrderBySortable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * Class OnboardingImage
 *
 * @property int $id
 * @property string $image_uz
 * @property string|null $image_ru
 * @property int $order
 * @property bool $is_active
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read string $image_url
 */
class OnboardingImage extends Model
{
    use HasFactory, OrderBySortable, TenantConnection;

    protected static array $sortables = ['id', 'order', 'is_active'];

    protected $table = 'loyalty_program_onboarding_images';

    protected $fillable = [
        'image_uz',
        'image_ru',
        'order',
        'is_active',
    ];

    public function getImageUrlAttribute()
    {
        $locale = app()->getLocale();

        return tenant_asset(
            $this->getAttribute("image_{$locale}") ?: $this->image_uz
        );
    }
}
