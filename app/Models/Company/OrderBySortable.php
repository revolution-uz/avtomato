<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * @method orderBySortable($sortString): static
 */
trait OrderBySortable
{
    public function scopeOrderBySortable(Builder $query, string $sort)
    {
        $fieldName = ltrim($sort, '-');
        $direction = Str::startsWith($sort, '-') ? 'desc' : 'asc';

        if (! in_array($fieldName, self::$sortables)) {
            throw ValidationException::withMessages(['sort' => 'Invalid sort parameter']);
        }

        if ($this->getConnection()->getSchemaBuilder()->hasColumn($this->getTable(), $fieldName)) {
            return $query->orderBy($fieldName, $direction);
        }

        if ($this->getConnection()->getSchemaBuilder()->hasColumn($this->getTable(), $fieldName . '_' . app()->getLocale())) {
            return $query->orderBy($fieldName, $direction . '_' . app()->getLocale());
        }
    }
}
