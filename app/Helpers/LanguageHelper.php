<?php

namespace App\Helpers;

class LanguageHelper
{
    public static function getLanguageName(string $languageCode): string
    {
        return match ($languageCode) {
            'en' => 'English',
            'uz' => 'O\'zbekcha',
            'oz' => 'Ўзбекча',
            default => 'Русский',
        };
    }

    public static function getIconCode(string $languageCode): string
    {
        return match ($languageCode) {
            'en' => 'us',
            'uz', 'oz' => 'uz',
            default => 'ru',
        };
    }
}
