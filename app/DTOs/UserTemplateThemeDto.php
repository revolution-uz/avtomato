<?php

namespace App\DTOs;

use Spatie\LaravelData\Data;

class UserTemplateThemeDto extends Data
{
    public function __construct(
        public string $layout_name = 'column',
        public string $theme_name = 'default',
        public string $menu_width = '266px',
        public string $column_style = 'card',
        public string $color = '#4e88f3',
        public bool $is_follow = false,
        public bool $fixed_header = true,
        public bool $fold_sidebar = false,
        public bool $show_progress_bar = true,
        public bool $show_footer = true,
        public bool $show_tabs = true,
        public string $tabs_bar_style = 'card',
        public bool $show_tabs_icon = true,
        public bool $show_language = true,
        public bool $show_refresh = true,
        public bool $show_theme = true,
        public bool $show_search = true,
        public bool $show_notice = true,
        public bool $show_full_screen = true,
        public bool $show_dark = true,
        public string $page_transition_style = '',
        public int $radius = 5,
        public bool $show_lock = true,
        public string $passcode = '',
    ) {
    }
}
