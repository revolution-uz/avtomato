<?php

namespace App\DTOs;

use Spatie\LaravelData\Data;

class UserTemplateThemeDto extends Data
{
    public function __construct(
        public string $layout_name,
        public string $theme_name,
        public string $menu_width,
        public string $column_style,
        public string $color,
        public bool $is_follow,
        public bool $fixed_header,
        public bool $fold_sidebar,
        public bool $show_progress_bar,
        public bool $show_footer,
        public bool $show_tabs,
        public string $tabs_bar_style,
        public bool $show_tabs_icon,
        public bool $show_language,
        public bool $show_refresh,
        public bool $show_theme,
        public bool $show_search,
        public bool $show_notice,
        public bool $show_full_screen,
        public bool $show_theme_setting,
        public bool $show_dark,
        public string $page_transition_style,
        public string $radius,
        public bool $show_lock,
        public bool $show_color_picker,
        public string $background,
        public string $passcode,
    )
    {
    }
}
