<?php

namespace Database\Seeders\Company;

use App\Models\Company;
use App\Models\Company\Ecommerce\Banner;
use App\Models\Company\Ecommerce\EcommerceImage;
use App\Models\Company\Ecommerce\Product;
use App\Models\Company\Ecommerce\ProductBrand;
use App\Models\Company\Ecommerce\ProductCategory;
use App\Models\Company\Ecommerce\ProductOption;
use App\Models\Company\Ecommerce\ProductOptionValue;
use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EcommerceSeeder extends Seeder
{
    private Collection $_brands;
    private Collection $_categories;
    private Collection $_products;
    private Collection $_options;

    public function run(): void
    {
        tenancy()->initialize(Company::findOrFail(Company::REVO_TENANT_ID));

        $this->seedImages();
        $this->seedBrands();
        $this->seedCategories();
        $this->seedProducts();
        $this->seedBanners();
        $this->seedOptions();
        $this->seedValues();
    }

    private function seedBanners()
    {
        $data = $this->parseCsv(database_path('seeders/data/banners.csv'));
        $existingIds = Banner::query()->whereIn('id', $data->pluck('id'))->pluck('id')->toArray();

        $newData = $data->reject(fn ($item) => in_array($item['id'], $existingIds))
            ->map(fn ($item) => [
                'id' => $item['id'],
                'name_uz' => 'name uz',
                'name_ru' => 'name ru',
                'name_en' => 'name en',
                'name_oz' => 'name oz',
                'url' => null,
                'is_active' => (bool) $item['status'],
                'category_id' => ! empty($item['category_id']) ? $this->_categories[$item['category_id']] : null,
                'brand_id' => null,
                'product_id' => ! empty($item['product_id']) ? $this->_products[$item['product_id']] : null,
                'image_uz_id' => $this->downloadImage('https://cdn.asaxiy.uz/asaxiy-content/uploads/banner/desktop', $item['img_uz'])?->id,
                'image_ru_id' => $this->downloadImage('https://cdn.asaxiy.uz/asaxiy-content/uploads/banner/desktop', $item['img_ru'])?->id,
                'image_oz_id' => $this->downloadImage('https://cdn.asaxiy.uz/asaxiy-content/uploads/banner/desktop', $item['img_ru'])?->id,
                'image_en_id' => $this->downloadImage('https://cdn.asaxiy.uz/asaxiy-content/uploads/banner/desktop', $item['img_uz'])?->id,
                'starts_at' => Carbon::createFromTimestampUTC($item['top_expires_at']),
                'ends_at' => Carbon::createFromTimestampUTC($item['expires_at']),
                'created_at' => Carbon::createFromTimestampUTC($item['created_at']),
                'updated_at' => Carbon::createFromTimestampUTC($item['updated_at']),
            ]);

        if ($newData->isNotEmpty()) {
            Banner::query()->insert($newData->toArray());
            $this->command->info('Banners data imported successfully!');
        } else {
            $this->command->info('No new Banners to import.');
        }
    }

    private function seedBrands(): void
    {
        $data = $this->parseCsv(database_path('seeders/data/brands.csv'));

        $existingSlugs = ProductBrand::query()->whereIn('slug', $data->pluck('slug'))->pluck('slug')->toArray();

        $newData = $data->reject(fn ($item) => in_array($item['slug'], $existingSlugs))
            ->map(fn ($item) => [
                'name_uz' => $item['name'],
                'name_ru' => $item['name'],
                'name_en' => $item['name'],
                'name_oz' => $item['name'],
                'is_active' => $item['status'],
                'slug' => $item['slug'],
                'image_id' => $this->downloadImage('https://assets.asaxiy.uz/brand', $item['image'])?->id,
                'created_at' => Carbon::createFromTimestampUTC($item['created_at']),
                'updated_at' => Carbon::createFromTimestampUTC($item['updated_at']),
            ]);

        if ($newData->isNotEmpty()) {
            ProductBrand::query()->insert($newData->toArray());
            $this->command->info('Brands data imported successfully!');
        } else {
            $this->command->info('No new Brands to import.');
        }

        $brandsBySlug = ProductBrand::query()->whereIn('slug', $data->pluck('slug'))->get()->keyBy('slug');
        $this->_brands = $data->mapWithKeys(function ($imported) use ($brandsBySlug) {
            if (isset($brandsBySlug[$imported['slug']])) {
                return [$imported['id'] => $brandsBySlug[$imported['slug']]->id];
            }

            return [];
        });
    }

    private function seedCategories()
    {
        $data = $this->parseCsv(database_path('seeders/data/categories.csv'));

        $existingSlugs = ProductCategory::query()->whereIn('slug', $data->pluck('slug'))->pluck('slug')->toArray();

        $newData = $data->reject(fn ($item) => in_array($item['slug'], $existingSlugs))
            ->map(fn ($item) => [
                'parent_id' => ! empty($item['parent_id']) ? $item['parent_id'] : null,
                'name_uz' => $item['name_uz'],
                'name_ru' => $item['name_ru'],
                'name_en' => $item['name_uz'],
                'name_oz' => $item['name_oz'],
                'slug' => $item['slug'],
                'is_active' => true,
                'image_id' => ! empty($item['image']) ? $this->downloadImage('https://assets.asaxiy.uz/category', $item['image'])?->id : null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

        if ($newData->isNotEmpty()) {
            ProductCategory::query()->insert($newData->toArray());
            $this->command->info('Categories data imported successfully!');
        } else {
            $this->command->info('No new Categories to import.');
        }

        $categoriesBySlug = ProductCategory::query()->whereIn('slug', $data->pluck('slug'))->get()->keyBy('slug');
        $this->_categories = $data->mapWithKeys(function ($imported) use ($categoriesBySlug) {
            if (isset($categoriesBySlug[$imported['slug']])) {
                return [$imported['id'] => $categoriesBySlug[$imported['slug']]->id];
            }

            return [];
        });
    }

    private function seedProducts()
    {
        $data = $this->parseCsv(database_path('seeders/data/products.csv'));

        $existingSlugs = Product::query()->whereIn('slug', $data->pluck('slug'))->pluck('slug')->toArray();

        $newData = $data->reject(fn ($item) => in_array($item['slug'], $existingSlugs))
            ->map(fn ($item) => [
                'name_uz' => $item['name_uz'],
                'name_ru' => $item['name_ru'],
                'name_en' => $item['name_uz'],
                'name_oz' => $item['name_ru'],
                'category_id' => $this->_categories[$item['category_id']],
                'brand_id' => $this->_brands[$item['brand_id']],
                'slug' => $item['slug'],
                'is_active' => true,
                'cost' => $item['cost'],
                'actual_price' => $item['actual_price'],
                'old_price' => $item['old_price'],
                'images' => $this->downloadProductImages($item['main_image'], json_decode($item['images'])),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

        if ($newData->isNotEmpty()) {
            foreach ($newData as $item) {
                /** @var Product $product */
                $product = Product::query()->create(Arr::except($item, 'images'));
                $product->syncImagesWithOrder($item['images']);
            }
            $this->command->info('Products CSV data imported successfully!');
        } else {
            $this->command->info('No new Products to import.');
        }

        $productsBySlug = Product::query()->whereIn('slug', $data->pluck('slug'))->get()->keyBy('slug');
        $this->_products = $data->mapWithKeys(function ($imported) use ($productsBySlug) {
            if (isset($productsBySlug[$imported['slug']])) {
                return [$imported['id'] => $productsBySlug[$imported['slug']]->id];
            }

            return [];
        });
    }

    private function seedImages(): void
    {
        $data = $this->parseCsv(database_path('seeders/data/images.csv'));

        $existingSlugs = EcommerceImage::query()
            ->whereIn('original_name', $data->pluck('original_name'))
            ->pluck('original_name')
            ->toArray();

        $newData = $data->reject(fn ($item) => in_array($item['original_name'], $existingSlugs))
            ->map(fn ($item) => [
                'path' => $item['path'],
                'original_name' => $item['original_name'],
                'mime' => $item['mime'],
                'created_at' => $item['created_at'],
                'updated_at' => $item['updated_at'],
            ]);

        $newData->each(function ($item) {
            if (! Storage::disk('public')->exists(EcommerceImage::UPLOADS_PATH . '/' . $item['path'])) {
                $this->command->warn("Image was not found: {$item['path']}");
            }
        });

        if ($newData->isNotEmpty()) {
            EcommerceImage::query()->insert($newData->toArray());
            $this->command->info('Images data imported successfully!');
        } else {
            $this->command->info('No new Images to import.');
        }
    }

    private function seedOptions(): void
    {
        $data = $this->parseCsv(database_path('seeders/data/options.csv'));

        $existingNames = ProductOption::query()->whereIn('name_uz', $data->pluck('name_uz'))->pluck('name_uz')->toArray();

        $newData = $data->reject(fn ($item) => in_array($item['name_uz'], $existingNames))
            ->map(fn ($item) => [
                'name_uz' => $item['name_uz'],
                'name_ru' => $item['name_ru'],
                'name_en' => $item['name_uz'],
                'name_oz' => $item['name_ru'],
                'slug' => Str::slug($item['name_uz']),
                'description' => 'description',
                'type' => $item['type'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

        if ($newData->isNotEmpty()) {
            ProductOption::query()->insert($newData->toArray());
            $this->command->info('Options data imported successfully!');
        } else {
            $this->command->info('No new Options to import.');
        }

        $optionsBySlug = ProductOption::query()->whereIn('name_uz', $data->pluck('name_uz'))->get()->keyBy('name_uz');
        $this->_options = $data->mapWithKeys(function ($imported) use ($optionsBySlug) {
            if (isset($optionsBySlug[$imported['name_uz']])) {
                return [$imported['id'] => $optionsBySlug[$imported['name_uz']]->id];
            }

            return [];
        });
    }

    private function seedValues(): void
    {
        $data = $this->parseCsv(database_path('seeders/data/values.csv'));

        $existingValues = ProductOptionValue::query()->whereIn('value_uz', $data->pluck('value_uz'))->pluck('value_uz')->toArray();

        $newData = $data->reject(fn ($item) => in_array($item['value_uz'], $existingValues))
            ->map(fn ($item) => [
                'value_uz' => $item['value_uz'],
                'value_ru' => $item['value_ru'],
                'value_en' => $item['value_uz'],
                'value_oz' => $item['value_ru'],
                'slug' => Str::slug($item['value_uz']),
                'option_id' => $this->_options[$item['option_id']],
                'image_id' => ! empty($item['image']) ? $this->downloadImage('', $item['image'])?->id : null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

        if ($newData->isNotEmpty()) {
            ProductOptionValue::query()->insert($newData->toArray());
            $this->command->info('Values data imported successfully!');
        } else {
            $this->command->info('No new Values to import.');
        }
    }

    private function parseCsv(string $filePath): Collection
    {
        if (! file_exists($filePath) || ! is_readable($filePath)) {
            throw new Exception("File not found or not readable at: {$filePath}");
        }

        $header = null;
        $data = collect();

        if (($handle = fopen($filePath, 'r')) !== false) {
            while (($row = fgetcsv($handle, 1000)) !== false) {
                if (! $header) {
                    $row[0] = preg_replace('/^\x{FEFF}/u', '', $row[0]);
                    $header = $row;
                } else {
                    $data->push(array_combine($header, $row));
                }
            }
            fclose($handle);
        }

        if ($data->isEmpty()) {
            throw new Exception('No valid data found in the CSV file.');
        }

        return $data;
    }

    private function downloadProductImages(...$items)
    {
        $images = [];
        foreach ($items as $item) {
            if (is_string($item)) {
                $image = $this->downloadImage('https://cdn.asaxiy.uz/asaxiy-content/product/main_image/desktop', $item)?->id;

                if ($image) {
                    $images[] = [
                        'image_id' => $image,
                        'order' => 1,
                    ];
                }

            } elseif (is_array($item)) {
                foreach ($item as $i) {
                    $image = $this->downloadImage('https://assets.asaxiy.uz/product/items/desktop', $i)?->id;

                    if ($image) {
                        $images[] = [
                            'image_id' => $image,
                            'order' => 2,
                        ];
                    }
                }
            }
        }

        return $images;
    }

    private function downloadImage(string $url, string $imagePath): ?EcommerceImage
    {
        try {
            $imagePath = basename($imagePath);

            if ($existingImage = EcommerceImage::query()->firstWhere('original_name', $imagePath)) {
                return $existingImage;
            }

            $this->command->info("Downloading {$imagePath}");

            $response = Http::get("{$url}/{$imagePath}");

            if (! $response->successful()) {
                $this->command->error("Failed to download image from URL: {$imagePath}");

                return null;
            }

            try {
                $tempFile = tmpfile();
                fwrite($tempFile, $response->body());
                $extension = pathinfo($imagePath, PATHINFO_EXTENSION) ?: 'jpg';
                $uniqueFilename = uniqid('', true) . '.' . $extension;

                if (! Storage::disk('public')->put(EcommerceImage::UPLOADS_PATH . '/' . $uniqueFilename, $response->body())) {
                    throw new Exception('Failed to save file to disk.');
                }

                $image = EcommerceImage::create([
                    'path' => $uniqueFilename,
                    'original_name' => $imagePath,
                    'mime' => Str::limit($response->header('Content-Type'), 64),
                ]);

                return $image;

            } catch (Exception $e) {
                $this->command->error("Failed to process the image: {$e->getMessage()}");

                return null;
            }

        } catch (Exception $e) {
            $this->command->error("Error downloading image from URL: {$imagePath}. Error: " . $e->getMessage());
        }

        return null;
    }
}
