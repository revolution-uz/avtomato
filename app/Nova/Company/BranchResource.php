<?php

namespace App\Nova\Company;

use App\Models\Company\Branch;
use App\Nova\Resource;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;

class BranchResource extends Resource
{
    public static $model = Branch::class;

    public static $title = 'name';

    public static $search = [
        'id', 'name', 'address', 'inn',
    ];

    public function fields(Request $request): array
    {
        return [
            ID::make()->sortable(),

            Text::make('Name')
                ->sortable()
                ->rules('required'),

            Text::make('Address')
                ->sortable()
                ->rules('required'),

            Text::make('Inn')
                ->sortable()
                ->rules('required'),
        ];
    }

    public function cards(Request $request): array
    {
        return [];
    }

    public function filters(Request $request): array
    {
        return [];
    }

    public function lenses(Request $request): array
    {
        return [];
    }

    public function actions(Request $request): array
    {
        return [];
    }
}
