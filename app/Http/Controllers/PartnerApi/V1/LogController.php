<?php

namespace App\Http\Controllers\PartnerApi\V1;

use App\Helpers\Swagger\OpenApiArrayResponse;
use App\Helpers\Swagger\OpenApiPageParameter;
use App\Helpers\Swagger\OpenApiPerPageParameter;
use App\Helpers\Swagger\OpenApiSearchParameter;
use App\Helpers\Swagger\OpenApiSortParameter;
use App\Http\Controllers\Controller;
use App\Http\Requests\LogFilterRequest;
use App\Http\Requests\OwnLogsRequest;
use App\Http\Resources\LogResource;
use App\Models\Company\ActivityLog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use OpenApi\Attributes as OA;

class LogController extends Controller
{
    #[OA\Get(
        path: '/api/v1/log',
        description: 'Get logs list',
        tags: ['ActivityLogs'],
        parameters: [
            new OpenApiPageParameter,
            new OpenApiPerPageParameter,
            new OpenApiSearchParameter,
            new OpenApiSortParameter,
            new OA\Parameter(
                name: 'user_id',
                description: 'ID of user',
                in: 'query',
                required: false
            ),
        ],
        responses: [
            new OpenApiArrayResponse(resourceName: 'LogResource', withPagination: true, withSortables: true),
        ]
    )]
    public function index(LogFilterRequest $request)
    {
        $sortables = ['id', 'created_at'];

        return LogResource::collection(
            ActivityLog::query()
                ->with(['causer'])
                ->when(
                    $request->search,
                    fn (Builder $q) => $q
                        ->where('description', 'ILIKE', '%' . $request->search . '%')
                )
                ->when(
                    $request->user_id,
                    fn (Builder $q) => $q
                        ->where('causer_id', $request->user_id)
                )
                ->when(
                    $request->sort && in_array($request->sort, $sortables),
                    fn (Builder $q) => Str::startsWith($request->sort, '-')
                        ? $q->orderBy(Str::substr($request->sort, 1), 'desc')
                        : $q->orderBy($request->sort)
                )
                ->paginate($request->per_page)
        )->additional(['sortables' => $sortables]);
    }

    #[OA\Get(
        path: '/api/v1/log/own',
        description: 'Get logs list',
        tags: ['ActivityLogs'],
        parameters: [
            new OpenApiPageParameter,
            new OpenApiPerPageParameter,
            new OpenApiSearchParameter,
            new OpenApiSortParameter,
        ],
        responses: [
            new OpenApiArrayResponse(resourceName: 'LogResource', withPagination: true, withSortables: true),
        ]
    )]
    public function ownLogs(OwnLogsRequest $request)
    {
        $sortables = ['id', 'created_at'];

        return LogResource::collection(
            ActivityLog::query()
                ->where('causer_id', auth()->user()->id)
                ->when(
                    $request->search,
                    fn (Builder $q) => $q
                        ->where('description', 'ILIKE', '%' . $request->search . '%')
                )
                ->when(
                    $request->sort && in_array($request->sort, $sortables),
                    fn (Builder $q) => Str::startsWith($request->sort, '-')
                        ? $q->orderBy(Str::substr($request->sort, 1), 'desc')
                        : $q->orderBy($request->sort)
                )
                ->paginate($request->per_page)
        )->additional(['sortables' => $sortables]);
    }
}
