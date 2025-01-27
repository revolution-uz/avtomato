<?php

namespace App\Traits;

use Illuminate\Support\Facades\App;

trait HasTranslations
{
    public function getTranslatedAttribute(string $key): ?string
    {
        $locale = App::getLocale();
        $localizedColumn = "{$key}_{$locale}";

        if (array_key_exists($localizedColumn, $this->attributes)) {
            return $this->getAttribute($localizedColumn);
        }

        return $this->getAttribute($key);
    }

    protected function getBaseKeyForTranslatedColumn(string $key): ?string
    {
        $localeSuffix = '_' . App::getLocale();
        $baseKey = str_replace($localeSuffix, '', $key);

        foreach ($this->attributes as $column => $value) {
            if (str_starts_with($column, $baseKey . '_') || $column === $baseKey) {
                return $baseKey;
            }
        }

        return null;
    }

    public function __get($key)
    {
        if (array_key_exists($key, $this->attributes)) {
            return parent::__get($key);
        }

        $baseKey = $this->getBaseKeyForTranslatedColumn($key);
        if ($baseKey) {
            return $this->getTranslatedAttribute($baseKey);
        }

        return parent::__get($key);
    }
}
