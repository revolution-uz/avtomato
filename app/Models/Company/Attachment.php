<?php

namespace App\Models\Company;

use App\Enums\Company\AttachmentTypeEnum;
use App\Traits\LogsActivityDirtyOnlyTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Stancl\Tenancy\Database\Concerns\TenantConnection;
use Throwable;

/**
 * @property string $id
 * @property AttachmentTypeEnum $type
 * @property string $path
 * @property string $name
 * @property string $original_name
 * @property string $mime
 * @property int $attachmentable_id
 * @property string $attachmentable_type
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 * @property-read string $url
 */
class Attachment extends Model
{
    use LogsActivityDirtyOnlyTrait, TenantConnection;

    public const UPLOADS_PATH = 'uploads';

    protected $fillable = [
        'id',
        'type',
        'path',
        'name',
        'original_name',
        'mime',
    ];

    protected $casts = [
        'type' => AttachmentTypeEnum::class,
    ];

    /**
     * @throws Throwable
     */
    public static function upload(UploadedFile $file, AttachmentTypeEnum $type, Model $model, ?string $name = null): self
    {
        $attachment = new self;
        $attachment->type = $type;
        $attachment->path = uniqid() . '.' . $file->extension();
        $attachment->name = $name;
        $attachment->original_name = $file->getBasename();
        $attachment->mime = Str::substr($file->getMimeType(), 0, 64);
        $attachment->attachmentable_type = $model::class;
        $attachment->attachmentable_id = $model->getKey();
        // storing file and preview
        $file->storeAs(self::UPLOADS_PATH . '/' . $type->getStoragePath(), $attachment->path, ['disk' => 'public']);
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
}
