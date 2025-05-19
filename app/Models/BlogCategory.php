<?php

namespace App\Models;

use App\Enums\MessageField;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name_en
 * @property string $name_ru
 * @property string $name_uz
 * @property string $slug
 * @property string $meta_description
 * @property string $meta_keywords
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 * @property-read string $name
 * @property-read string $seo_description
 * @property-read string $seo_keywords
 */
class BlogCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name_en',
        'name_ru',
        'name_uz',
        'meta_description',
        'meta_keywords',
        'slug',
    ];

    protected $casts = [
        'meta_keywords' => MessageField::class,
        'meta_description' => MessageField::class,
    ];

    public function blogs(): HasMany
    {
        return $this->hasMany(Blog::class, 'category_id', 'id');
    }

    public function getNameAttribute(): string
    {
        return $this->{'name_' . app()->getLocale()};
    }

    public function getSeoDescriptionAttribute(): string
    {
        return $this->meta_description->{'message_' . app()->getLocale()};
    }

    public function getSeoKeywordsAttribute(): string
    {
        return $this->meta_keywords->{'message_' . app()->getLocale()};
    }
}
