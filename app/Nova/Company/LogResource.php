<?php

namespace App\Nova\Company;

use App\Models\Company\ActivityLog;
use App\Nova\Resource;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\MorphTo;
use Laravel\Nova\Fields\Text;

class LogResource extends Resource
{
    public static $model = ActivityLog::class;

    public static $title = 'id';

    public static $search = [
        'id', 'event',
    ];

    public function fields(Request $request): array
    {
        return [
            ID::make()->sortable(),

            Text::make('Event')
                ->sortable()
                ->rules('nullable'),

            MorphTo::make('User', 'causer'),

            MorphTo::make('Subject', 'subject'),
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
