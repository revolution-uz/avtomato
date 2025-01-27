<?php

namespace App\Traits;

trait HasTranslations
{
    public function getTranslatedAttribute(string $key): ?string
    {
        $column = $key . '_' . app()->getLocale();

        return $this->getAttribute($column) ?? $this->getAttribute($key);
    }

    public function __get($key)
    {
        if (in_array($key, $this->translatedColumns ?? [])) {
            return $this->getTranslatedAttribute($key);
        }

        return parent::__get($key);
    }
}
