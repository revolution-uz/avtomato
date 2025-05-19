<?php

namespace App\DTOs\Ecommerce\Product;

use App\DTOs\Ecommerce\Base\BaseEcommerceDTO;

/**
 * Class EcommerceProductFilterOptionRequestDTO
 *
 * @property int $option_id
 * @property int $value_id
 */
class EcommerceProductFilterOptionRequestDTO extends BaseEcommerceDTO
{
    public function __construct(
        public int $option_id,
        public int $value_id,
    ) {}
}
