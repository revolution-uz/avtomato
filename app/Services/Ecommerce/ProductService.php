<?php

namespace App\Services\Ecommerce;

use App\Models\Company\Ecommerce\EcommerceImage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Throwable;

class ProductService
{
    /**
     * @throws Throwable
     */
    public function uploadImage(Model $model, UploadedFile $file): Model
    {
        EcommerceImage::uploadImage($file, $model);
        $model->refresh();

        return $model;
    }
}
