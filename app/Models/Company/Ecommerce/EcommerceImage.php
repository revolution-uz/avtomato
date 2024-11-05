<?php

namespace App\Models\Company\Ecommerce;

use App\Traits\LogsActivityDirtyOnlyTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
 * @property int $attachmentable_id
 * @property string $attachmentable_type
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 * @property-read string $url
 */
class EcommerceImage extends Model
{
    use HasFactory, LogsActivityDirtyOnlyTrait, TenantConnection;
    public const string UPLOADS_PATH = 'uploads/ecommerce';
    public $table = 'e_commerce_images';

    protected $fillable = [
        'id',
        'path',
        'original_name',
        'mime',
    ];

    /**
     * @throws Throwable
     */
    public static function uploadImage(UploadedFile $file, Model $model): self
    {
        $ecommerceImage = new self;
        $ecommerceImage->path = uniqid() . '.' . $file->extension();
        $ecommerceImage->original_name = $file->getBasename();
        $ecommerceImage->mime = Str::substr($file->getMimeType(), 0, 64);
        $ecommerceImage->attachmentable_type = $model::class;
        $ecommerceImage->attachmentable_id = $model->getKey();
        // storing file and preview
        $file->storeAs(self::UPLOADS_PATH, $ecommerceImage->path, ['disk' => 'public']);
        // saving images
        $ecommerceImage->saveOrFail();

        return $ecommerceImage;
    }

    public function url(): Attribute
    {
        return Attribute::make(
            get: fn () => asset(self::UPLOADS_PATH . '/' . $this->path)
        );
    }
}
