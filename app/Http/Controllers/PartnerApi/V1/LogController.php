<?php

namespace App\Http\Controllers\PartnerApi\V1;

use App\Helpers\Swagger\OpenApiArrayResponse;
use App\Helpers\Swagger\OpenApiDataResponse;
use App\Helpers\Swagger\OpenApiIdParameter;
use App\Helpers\Swagger\OpenApiNotFoundResponse;
use App\Helpers\Swagger\OpenApiPageParameter;
use App\Helpers\Swagger\OpenApiPerPageParameter;
use App\Helpers\Swagger\OpenApiSearchParameter;
use App\Helpers\Swagger\OpenApiSortParameter;
use App\Http\Controllers\Controller;
use App\Http\Requests\LogFilterRequest;
use App\Http\Requests\LogViewRequest;
use App\Http\Requests\OwnLogsRequest;
use App\Http\Resources\LogResource;
use App\Models\Company\ActivityLog;
use Illuminate\Database\Eloquent\Builder;
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
        $request->sortables = ['id', 'created_at'];

        return LogResource::collection(
            ActivityLog::query()
                ->with(['causer'])
                ->when($request->search, function (Builder $q) use ($request) {
                    $search = '%' . $request->search . '%';
                    $q->where(function (Builder $query) use ($search) {
                        $query->where('description', 'ILIKE', $search);
                    });
                })
                ->when(
                    $request->user_id,
                    fn (Builder $q) => $q
                        ->where('causer_id', $request->user_id)
                )
                ->when($request->isSortable(), function (Builder $q) use ($request) {
                    $q->orderBy($request->sortField(), $request->sortDirection());
                })
                ->paginate($request->per_page)
        )->additional(['sortables' => $request->sortables]);
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
        $request->sortables = ['id', 'created_at'];

        return LogResource::collection(
            ActivityLog::query()
                ->where('causer_id', auth()->user()->id)
                ->when($request->search, function (Builder $q) use ($request) {
                    $search = '%' . $request->search . '%';
                    $q->where(function (Builder $query) use ($search) {
                        $query->where('description', 'ILIKE', $search);
                    });
                })
                ->when($request->isSortable(), function (Builder $q) use ($request) {
                    $q->orderBy($request->sortField(), $request->sortDirection());
                })
                ->paginate($request->per_page)
        )->additional(['sortables' => $request->sortables]);
    }

    #[OA\Get(
        path: '/api/v1/log/{id}',
        description: 'Get single log route',
        tags: ['ActivityLogs'],
        parameters: [
            new OpenApiIdParameter(modelName: 'ActivityLog'),
        ],
        responses: [
            new OpenApiDataResponse(resourceName: 'LogResource'),
            new OpenApiNotFoundResponse,
        ]
    )]
    public function getSingle(int $id, LogViewRequest $request): LogResource
    {
        return new LogResource(ActivityLog::findOrFail($id));
    }
}
