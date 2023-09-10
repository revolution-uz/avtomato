<?php

namespace App\DTOs\Common;

class MenuItem
{
    /**
     * @param string|null $name
     * @param string|null $icon
     * @param string|string[]|null $url
     * @param string|null $target
     * @param string|null $menuHeader
     * @param self[]|null $subMenu
     */
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?string $icon = null,
        public readonly string|array|null $url = null,
        public readonly ?string $target = null,
        public readonly ?string $menuHeader = null,
        public readonly ?array $subMenu = null,
    )
    {
    }
}
