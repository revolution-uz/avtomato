<?php

namespace App\Http\Controllers\PartnerApi\V1\Scoring;

use App\Helpers\Swagger\OpenApiJsonArrayResponse;
use App\Helpers\Swagger\OpenApiPageParameter;
use App\Helpers\Swagger\OpenApiPerPageParameter;
use App\Helpers\Swagger\OpenApiSearchParameter;
use App\Helpers\Swagger\OpenApiSortParameter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Scoring\MibScoringFilterRequest;
use App\Http\Resources\Scoring\MibScoringResource;
use App\Models\Company\MibScoring;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use OpenApi\Attributes as OA;

class MibScoringController extends Controller
{
    #[OA\Get(
        path: '/api/v1/scoring/mib-scoring',
        description: 'Get Mib scoring requests list',
        tags: ['Scoring (Mib-scoring)'],
        parameters: [
            new OpenApiPageParameter,
            new OpenApiPerPageParameter,
            new OpenApiSearchParameter,
            new OpenApiSortParameter,
        ],
        responses: [
            new OpenApiJsonArrayResponse(resourceName: 'MibScoringResource', withPagination: true, withSortables: true),
        ]
    )]
    public function index(MibScoringFilterRequest $request)
    {
        $sortables = ['id', 'customer_id', 'amount'];

        return MibScoringResource::collection(
            MibScoring::query()
                ->when(
                    $request->search,
                    fn (Builder $q) => $q
                        ->orWhere('request->id', $request->search)
                        ->orWhere('request->customer_id', $request->search)
                        ->orWhere('request->amount', $request->search)
                )
                ->when(
                    $request->sort && in_array($request->sort, $sortables),
                    fn (Builder $q) => Str::startsWith($request->sort, '-')
                        ? $q->orderBy(Str::substr($request->sort, 1), 'desc')
                        : $q->orderBy($request->sort)
                )
                ->paginate($request->per_page)
        )->additional(['success' => true, 'sortables' => $sortables]);
    }
}
