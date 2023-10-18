<?php

namespace App\Http\Requests\Scoring;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string $sort
 * @property string $search
 * @property int $page
 * @property int $per_page
 */
class ScoringFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sort' => 'string|regex:/^[-]?[a-z]+$/',
            'search' => 'string|max:255',
            'page' => 'int|min:0',
            'per_page' => 'int|min:1',
        ];
    }
}
