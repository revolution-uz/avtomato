<?php

namespace App\DTOs\Ecommerce\Product;

use App\DTOs\Ecommerce\Base\BaseEcommerceDTO;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\DataCollection;

/**
 * Class EcommerceProductFilterRequestDTO
 *
 * @property int|null $category_id
 * @property int|null $brand_id
 * @property float|null $min_price
 * @property float|null $max_price
 */
class EcommerceProductFilterRequestDTO extends BaseEcommerceDTO
{
    public function __construct(
        public ?int $category_id,
        public ?int $brand_id,
        public ?float $min_price,
        public ?float $max_price,
        #[DataCollectionOf(EcommerceProductFilterOptionRequestDTO::class)]
        public ?DataCollection $options,
    ) {}

    public function isFilterableOption(): bool
    {
        return $this->options != null && is_array($this->options) && count($this->options);
    }
}
