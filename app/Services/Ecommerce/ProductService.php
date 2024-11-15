<?php

namespace App\Services\Ecommerce;

use App\DTOs\Ecommerce\Base\BaseEcommerceFilterRequestDTO;
use App\DTOs\Ecommerce\Product\EcommerceProductFilterRequestDTO;
use App\Models\Company\Ecommerce\EcommerceImage;
use App\Models\Company\Ecommerce\Product;
use Illuminate\Database\Eloquent\Builder;
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

    public function filtering(BaseEcommerceFilterRequestDTO $ecommerceFilterRequestDTO, ?EcommerceProductFilterRequestDTO $productFilterRequestDTO)
    {
        $query = Product::query()
            ->when(isset($ecommerceFilterRequestDTO->search), function (Builder $q) use ($ecommerceFilterRequestDTO) {
                $searchWords = explode(' ', $ecommerceFilterRequestDTO->search);
                foreach ($searchWords as $searchWord) {
                    $search = '%' . $searchWord . '%';
                    $q->where(function (Builder $query) use ($search) {
                        $query->where('name_en', 'ILIKE', $search)
                            ->orWhere('name_uz', 'ILIKE', $search)
                            ->orWhere('name_oz', 'ILIKE', $search)
                            ->orWhere('name_ru', 'ILIKE', $search);
                    });
                }
            });
        if ($productFilterRequestDTO !== null) {
            $query->when(isset($productFilterRequestDTO->category_id), function (Builder $q) use ($productFilterRequestDTO) {
                $q->where(function (Builder $query) use ($productFilterRequestDTO) {
                    $query->where('category_id', $productFilterRequestDTO->category_id);
                });
            })
                ->when(isset($productFilterRequestDTO->brand_id), function (Builder $q) use ($productFilterRequestDTO) {
                    $q->where(function (Builder $query) use ($productFilterRequestDTO) {
                        $query->where('brand_id', $productFilterRequestDTO->brand_id);
                    });
                })
                ->when(isset($productFilterRequestDTO->min_price), function (Builder $q) use ($productFilterRequestDTO) {
                    $q->where(function (Builder $query) use ($productFilterRequestDTO) {
                        $query->where('actual_price', '>=', $productFilterRequestDTO->min_price);
                    });
                })
                ->when(isset($productFilterRequestDTO->max_price), function (Builder $q) use ($productFilterRequestDTO) {
                    $q->where(function (Builder $query) use ($productFilterRequestDTO) {
                        $query->where('actual_price', '<=', $productFilterRequestDTO->max_price);
                    });
                });
        }

        return $query->when(
            isset($ecommerceFilterRequestDTO->sort),
            fn (Builder|Product $q) => $q->orderBySortable($ecommerceFilterRequestDTO->sort),
            fn (Builder|Product $q) => $q->orderByDesc('id')
        )
            ->paginate(perPage: $ecommerceFilterRequestDTO->per_page ?? 10, page: $ecommerceFilterRequestDTO->page ?? 1);
    }
}
