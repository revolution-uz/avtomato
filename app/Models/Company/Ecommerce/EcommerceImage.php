<?php

namespace App\Models\Company\Ecommerce;

use App\Traits\LogsActivityDirtyOnlyTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Stancl\Tenancy\Database\Concerns\TenantConnection;
use Throwable;

/**
 * @property string $id
 * @property string $path
 * @property string $original_name
 * @property string $mime
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 * @property-read string $url
 */
class EcommerceImage extends Model
{
    use HasFactory, LogsActivityDirtyOnlyTrait, SoftDeletes, TenantConnection;

    public const string UPLOADS_PATH = 'uploads/ecommerce';

    public $table = 'ecommerce_images';

    protected $fillable = [
        'path',
        'original_name',
        'mime',
    ];

    /**
     * @throws Throwable
     */
    public static function upload(UploadedFile $file): self
    {
        $image = new self;
        $image->path = uniqid() . '.' . $file->extension();
        $image->original_name = $file->getBasename();
        $image->mime = Str::substr($file->getMimeType(), 0, 64);
        // storing file and preview
        $file->storeAs(self::UPLOADS_PATH, $image->path, ['disk' => 'public']);
        // saving images
        $image->saveOrFail();

        return $image;
    }

    public static function boot()
    {
        parent::boot();

        static::forceDeleting(function (self $image) {
            if (Storage::disk('public')->exists(self::UPLOADS_PATH . '/' . $image->path)) {
                Storage::disk('public')->delete(self::UPLOADS_PATH . '/' . $image->path);
            }
        });
    }

    public function url(): Attribute
    {
        return Attribute::make(
            get: fn () => asset(self::UPLOADS_PATH . '/' . $this->path)
        );
    }
}
