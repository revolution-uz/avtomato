<?php

namespace App\Models\Company\Ecommerce;

use App\Traits\LogsActivityDirtyOnlyTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Stancl\Tenancy\Database\Concerns\TenantConnection;
use Throwable;

/**
 * @property string $id
 * @property string $path
 * @property string $original_name
 * @property string $mime
 * @property int $imagable_id
 * @property string $imagable_type
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 * @property-read string $url
 */
class Image extends Model
{
    use LogsActivityDirtyOnlyTrait, SoftDeletes, TenantConnection;

    public const string UPLOADS_PATH = 'uploads/ecommerce';

    public $table = 'ecommerce_images';

    protected $fillable = [
        'path',
        'original_name',
        'mime',
        'imagable_id',
        'imagable_type',
    ];

    /**
     * @throws Throwable
     */
    public static function upload(UploadedFile $file, Model $model): self
    {
        $image = new self;
        $image->path = uniqid() . '.' . $file->extension();
        $image->original_name = $file->getBasename();
        $image->mime = Str::substr($file->getMimeType(), 0, 64);
        $image->imagable_type = $model::class;
        $image->imagable_id = $model->getKey();
        // storing file and preview
        $file->storeAs(self::UPLOADS_PATH, $image->path, ['disk' => 'public']);
        // saving images
        $image->saveOrFail();

        return $image;
    }

    public function url(): Attribute
    {
        return Attribute::make(
            get: fn () => asset(self::UPLOADS_PATH . '/' . $this->path)
        );
    }
}
