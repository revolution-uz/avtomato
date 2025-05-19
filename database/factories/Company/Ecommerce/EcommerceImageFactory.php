<?php

namespace Database\Factories\Company\Ecommerce;

use App\Models\Company\Ecommerce\EcommerceImage;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;

class EcommerceImageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'path' => uniqid() . '.jpg',
            'original_name' => $this->faker->word . '.jpg',
            'mime' => 'image/jpeg',
        ];
    }

    public function withImageFile()
    {
        return $this->afterCreating(function (EcommerceImage $image) {
            UploadedFile::fake()
                ->image($image->original_name)
                ->storeAs(EcommerceImage::UPLOADS_PATH, $image->path, ['disk' => 'public']);
        });
    }
}
