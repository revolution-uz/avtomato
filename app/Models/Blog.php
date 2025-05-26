<?php

namespace App\Models;

use App\Enums\MessageField;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $slug
 * @property string $title_en
 * @property string $title_ru
 * @property string $title_uz
 * @property string $body_en
 * @property string $body_ru
 * @property string $body_uz
 * @property string $image_en
 * @property string|null $image_ru
 * @property string|null $image_uz
 * @property MessageField $meta_description
 * @property MessageField $meta_keywords
 * @property int $category_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 * @property BlogCategory $category
 * @property-read string $title
 * @property-read string $body
 * @property-read string $short_body
 * @property-read string $image_url
 * @property-read string $seo_description
 * @property-read string $seo_keywords
 */
class Blog extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'slug',
        'title_uz',
        'title_en',
        'title_ru',
        'body_uz',
        'body_en',
        'body_ru',
        'image_uz',
        'image_en',
        'image_ru',
        'meta_description',
        'meta_keywords',
        'category_id',
    ];

    protected $casts = [
        'meta_keywords' => MessageField::class,
        'meta_description' => MessageField::class,
    ];

    public function getSeoDescriptionAttribute(): string
    {
        return $this->meta_description->{'message_' . app()->getLocale()};
    }

    public function getSeoKeywordsAttribute(): string
    {
        return $this->meta_keywords->{'message_' . app()->getLocale()};
    }

    public function getShortBodyAttribute(): string
    {
        return Str::limit($this->body);
    }

    public function getTitleAttribute(): string
    {
        return $this->{'title_' . app()->getLocale()};
    }

    public function getBodyAttribute(): string
    {
        return $this->{'body_' . app()->getLocale()};
    }

    public function getImageUrlAttribute(): string
    {
        return Storage::url($this->{'image_' . app()->getLocale()} ?? $this->image_en);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'category_id')->withTrashed();
    }
}
