<?php

namespace App\DTOs\Ecommerce\Product;

use App\DTOs\Ecommerce\Base\BaseEcommerceDTO;

/**
 * Class EcommerceProductFilterRequestDTO
 *
 * @property int|null $category_id
 * @property int|null $brand_id
 * @property float|null $min_price
 * @property float|null $max_price
 * @property array|null $option_values
 */
class EcommerceProductFilterRequestDTO extends BaseEcommerceDTO
{
    public function __construct(
        public ?int $category_id,
        public ?int $brand_id,
        public ?float $min_price,
        public ?float $max_price,
        public ?array $option_values,
    ) {}

    public function isFilterableOption(): bool
    {
        return is_array($this->option_values) && count($this->option_values) > 0;
    }
}
