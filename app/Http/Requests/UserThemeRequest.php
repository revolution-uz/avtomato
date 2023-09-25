<?php

namespace App\Http\Requests;

use App\DTOs\UserTemplateThemeDto;
use Illuminate\Foundation\Http\FormRequest;
use Spatie\LaravelData\WithData;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'UserThemeRequest',
    title: 'Theme update body',
    properties: [
        new OA\Property(property: 'layout_name', type: 'string', example: 'column'),
        new OA\Property(property: 'theme_name', type: 'string', example: 'default'),
        new OA\Property(property: 'menu_width', type: 'string', example: '299px'),
        new OA\Property(property: 'column_style', type: 'string', example: 'horizontal'),
        new OA\Property(property: 'color', type: 'string', example: '#fff'),
        new OA\Property(property: 'is_follow', type: 'boolean', example: true),
        new OA\Property(property: 'fixed_header', type: 'boolean', example: true),
        new OA\Property(property: 'fold_sidebar', type: 'boolean', example: true),
        new OA\Property(property: 'show_progress_bar', type: 'boolean', example: true),
        new OA\Property(property: 'show_footer', type: 'boolean', example: true),
        new OA\Property(property: 'show_tabs', type: 'boolean', example: true),
        new OA\Property(property: 'tabs_bar_style', type: 'string', example: 'card'),
        new OA\Property(property: 'show_tabs_icon', type: 'boolean', example: true),
        new OA\Property(property: 'show_language', type: 'boolean', example: true),
        new OA\Property(property: 'show_refresh', type: 'boolean', example: true),
        new OA\Property(property: 'show_theme', type: 'boolean', example: true),
        new OA\Property(property: 'show_search', type: 'boolean', example: true),
        new OA\Property(property: 'show_notice', type: 'boolean', example: true),
        new OA\Property(property: 'show_full_screen', type: 'boolean', example: true),
        new OA\Property(property: 'show_theme_setting', type: 'boolean', example: true),
        new OA\Property(property: 'show_dark', type: 'boolean', example: true),
        new OA\Property(property: 'page_transition_style', type: 'string', example: 'el-fade-in'),
        new OA\Property(property: 'radius', type: 'string', example: '25'),
        new OA\Property(property: 'show_lock', type: 'boolean', example: true),
        new OA\Property(property: 'show_color_picker', type: 'boolean', example: true),
        new OA\Property(property: 'background', type: 'string', example: 'none'),
        new OA\Property(property: 'language', type: 'string', example: 'ru'),
        new OA\Property(property: 'passcode', type: 'string', example: '1234'),
    ],
)]
class UserThemeRequest extends FormRequest
{
    use WithData;

    protected function dataClass(): string
    {
        return UserTemplateThemeDto::class;
    }

    public function rules(): array
    {
        return [

        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
