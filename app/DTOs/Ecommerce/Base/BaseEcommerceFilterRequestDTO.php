<?php

namespace App\DTOs\Ecommerce\Base;

/**
 * Class BaseEcommerceFilterRequestDTO
 *
 * @property string|null $search
 * @property string|null $sort
 * @property int|null $page
 * @property int|null $per_page
 */
class BaseEcommerceFilterRequestDTO extends BaseEcommerceDTO
{
    public function __construct(
        public ?string $search,
        public ?string $sort,
        public ?int $page,
        public ?int $per_page,
    ) {}
}
