<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

/**
 * @property string $sort
 * @property string $search
 * @property int $page
 * @property int $per_page
 */
class BaseFilterRequest extends FormRequest
{
    public $sortables = [];
    public $languageAbleFields = ['name', 'title', 'description'];

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sort' => 'string|regex:/^[-]?[a-z_]+$/',
            'search' => 'string|max:255',
            'page' => 'int|min:0',
            'per_page' => 'int|min:1',
        ];
    }

    public function getSort()
    {
        return $this->input('sort', '-id');
    }

    public function isSortable(): bool
    {
        return in_array($this->sortField(), $this->sortables);
    }

    public function isLanguageAbleField()
    {
        return in_array($this->sortField(), $this->languageAbleFields);
    }

    public function sortField(): string
    {
        return ltrim($this->getSort(), '-');
    }

    public function languageAbleField(): string
    {
        return $this->sortField() . '_' . app()->getLocale();
    }

    public function sortDirection(): string
    {
        return Str::startsWith($this->getSort(), '-') ? 'desc' : 'asc';
    }

    public function getFieldAndDirection()
    {
        if ($this->isLanguageAbleField()) {
            return [$this->languageAbleField(), $this->sortDirection()];
        } else {
            return [$this->sortField(), $this->sortDirection()];
        }
    }
}
