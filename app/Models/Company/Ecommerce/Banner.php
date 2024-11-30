<?php

namespace App\Models\Company\Ecommerce;

use App\Models\Company\OrderBySortable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

/**
 * @property int $id
 * @property string $name_uz
 * @property string $name_ru
 * @property string $name_oz
 * @property string $name_en
 * @property string $image_uz_url
 * @property string $image_ru_url
 * @property string $image_oz_url
 * @property string $image_en_url
 * @property bool $is_active
 * @property string|null $url
 * @property int|null $category_id
 * @property int|null $brand_id
 * @property int|null $product_id
 * @property Image $image
 * @property-read ProductCategory $category
 * @property-read ProductBrand $brand
 * @property-read Product $product
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 */
class Banner extends Model
{
    use HasFactory, OrderBySortable, SoftDeletes;

    protected static array $sortables = [
        'id',
        'name_uz',
        'name_ru',
        'name_en',
        'name_oz',
        'url',
        'is_active',
        'category_id',
        'brand_id',
        'product_id',
        'created_at',
        'updated_at',
    ];

    const string UPLOADS_PATH = 'uploads/ecommerce/banner';

    protected $table = 'ecommerce_banners';

    protected $fillable = [
        'name_uz',
        'name_ru',
        'name_en',
        'name_oz',
        'is_active',
        'url',
        'category_id',
        'brand_id',
        'product_id',
    ];

    public function getImageUzUrlAttribute(): string
    {
        return $this->getImageUrlAttribute('uz');
    }

    public function getImageRuUrlAttribute(): string
    {
        return $this->getImageUrlAttribute('ru');
    }

    public function getImageEnUrlAttribute(): string
    {
        return $this->getImageUrlAttribute('en');
    }

    public function getImageOzUrlAttribute(): string
    {
        return $this->getImageUrlAttribute('oz');
    }

    public function getImageUrlAttribute(string $locale): string
    {
        $localizedPath = self::UPLOADS_PATH . '/' . $locale . '/' . $this->image->path;

        if (! Storage::disk('public')->exists($localizedPath)) {
            $localizedPath = self::UPLOADS_PATH . '/uz/' . $this->image->path; // Fallback to 'uz'
        }

        return asset($localizedPath);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(ProductBrand::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function image(): MorphOne
    {
        return $this->morphOne(Image::class, 'imagable');
    }

    /**
     * @throws Throwable
     */
    public function uploadImage(UploadedFile $file, ?string $locale, ?string $imageName = null): string
    {
        $image = $this->image()->firstOrCreate([
            'path' => is_null($imageName) ? uniqid() . '.' . $file->extension() : $imageName,
        ], [
            'original_name' => $file->getBasename(),
            'mime' => Str::substr($file->getMimeType(), 0, 64),
        ]);

        $file->storeAs(self::UPLOADS_PATH . '/' . $locale, $image->path, ['disk' => 'public']);

        return $image->path;
    }
}
