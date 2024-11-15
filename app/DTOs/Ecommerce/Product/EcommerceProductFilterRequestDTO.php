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
 */
class EcommerceProductFilterRequestDTO extends BaseEcommerceDTO
{
    public function __construct(
        public ?int $category_id,
        public ?int $brand_id,
        public ?float $min_price,
        public ?float $max_price,
    ) {}
}
