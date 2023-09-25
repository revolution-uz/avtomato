<?php

namespace App\Models\Company;

use App\Enums\Company\AttachmentTypeEnum;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Storage;

/**
 * @property string $id
 * @property string $type
 * @property string $preview_path
 * @property string $path
 * @property string $name
 * @property string $original_name
 * @property string $mime
 * @property int $attachmentable_id
 * @property string $attachmentable_type
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 *
 * @property-read Model $attachmentable
 */
class Attachment extends Model
{
    use HasUuids;

    public const PATH = 'uploads';
    public const THUMBNAIL_PATH = 'uploads/thumbnails';

    protected $fillable = [
        'id',
        'type',
        'preview_path',
        'path',
        'name',
        'original_name',
        'mime',
    ];

    protected $casts = [
        'type' => AttachmentTypeEnum::class,
    ];

    public function attachmentable()
    {
        return $this->morphTo();
    }

    public function link(): Attribute
    {
        return Attribute::make(
            get: fn() => Storage::disk('public')->url($this->path)
        );
    }

    public function previewLink(): Attribute
    {
        return Attribute::make(
            get: fn() => Storage::disk('public')->url($this->preview_path)
        );
    }
}
