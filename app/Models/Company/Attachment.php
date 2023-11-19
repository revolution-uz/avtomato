<?php

namespace App\Models\Company;

use App\Enums\Company\AttachmentTypeEnum;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * @property string $id
 * @property AttachmentTypeEnum $type
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
 * @property-read string $url
 * @property-read string $preview_url
 */
class Attachment extends Model
{
    use TenantConnection;

    public const UPLOADS_PATH = 'uploads';
    public const PREVIEW_PATH = 'thumbnails';

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

    public static function upload(UploadedFile $file, AttachmentTypeEnum $type, Model $model, string $name = null): self
    {
        $attachment = new self;
        $attachment->type = $type;
        $attachment->path = uniqid() . '.' . $file->extension();
        $attachment->preview_path = uniqid() . '.webp';
        $attachment->name = $name;
        $attachment->original_name = $file->getBasename();
        $attachment->mime = Str::substr($file->getMimeType(), 0, 64);
        $attachment->attachmentable_type = $model::class;
        $attachment->attachmentable_id = $model->getKey();
        // storing file and preview
        $file->storeAs(self::UPLOADS_PATH . '/' . $type->getStoragePath(), $attachment->path, ['disk' => 'public']);
        $thumbnail = Image::make($file)->resize(300, 300);
        Storage::disk('public')->put(
            self::PREVIEW_PATH . '/' . $type->getStoragePath() . '/' . $attachment->preview_path,
            $thumbnail->encode('webp')
        );
        // saving attachment
        $attachment->saveOrFail();

        return $attachment;
    }

    public function url(): Attribute
    {
        return Attribute::make(
            get: fn () => asset(Attachment::UPLOADS_PATH . '/' . $this->type->getStoragePath() . '/' . $this->path)
        );
    }

    public function previewUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => asset(Attachment::PREVIEW_PATH . '/' . $this->type->getStoragePath() . '/' . $this->preview_path)
        );
    }
}
