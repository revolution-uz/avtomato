<?php

namespace App\Http\Controllers\PartnerApi\V1\Management;

use App\Helpers\Swagger\OpenApiIdParameter;
use App\Helpers\Swagger\OpenApiJsonArrayResponse;
use App\Helpers\Swagger\OpenApiJsonResponse;
use App\Helpers\Swagger\OpenApiNotFoundResponse;
use App\Helpers\Swagger\OpenApiPageParameter;
use App\Helpers\Swagger\OpenApiPerPageParameter;
use App\Helpers\Swagger\OpenApiSearchParameter;
use App\Helpers\Swagger\OpenApiSingleDataResponse;
use App\Helpers\Swagger\OpenApiSortParameter;
use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Requests\Management\BranchesFilterRequest;
use App\Http\Requests\Management\BranchRequest;
use App\Http\Resources\Management\BranchResource;
use App\Models\Company\Branch;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use OpenApi\Attributes as OA;

class BranchController extends Controller
{
    #[OA\Get(
        path: '/api/v1/management/branches',
        description: 'Get branches list route',
        tags: ['Branches'],
        parameters: [
            new OpenApiPageParameter(),
            new OpenApiPerPageParameter(),
            new OpenApiSearchParameter(),
            new OpenApiSortParameter(),
        ],
        responses: [
            new OpenApiJsonArrayResponse(resourceName: 'BranchResource'),
        ]
    )]
    public function index(BranchesFilterRequest $request)
    {
        return BranchResource::collection(
            Branch::query()
                ->when(
                    $request->search,
                    fn(Builder $q) => $q
                        ->where('name', 'ILIKE', '%' . $request->search . '%')
                        ->where('address', 'ILIKE', '%' . $request->search . '%')
                        ->where('inn', 'ILIKE', '%' . $request->search . '%')
                )
                ->when(
                    $request->sort,
                    fn(Builder $q) => Str::startsWith($request->sort, '-')
                        ? $q->orderBy(Str::substr($request->sort, 1), 'desc')
                        : $q->orderBy($request->sort)
                )
                ->paginate($request->per_page)
        )->additional(['success' => true]);
    }

    #[OA\Get(
        path: '/api/v1/management/branches/{id}',
        description: 'Get single branch route',
        tags: ['Branches'],
        parameters: [
            new OpenApiIdParameter(modelName: 'Branch'),
        ],
        responses: [
            new OpenApiJsonResponse(resourceName: 'BranchResource'),
            new OpenApiNotFoundResponse(),
        ]
    )]
    public function getSingle(Branch $branch): BranchResource
    {
        return (new BranchResource($branch))->additional(['success' => true]);
    }

    #[OA\Post(
        path: '/api/v1/management/branches',
        description: 'Create branch route',
        requestBody: new OA\RequestBody(
            description: 'Create branch request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/BranchRequest')
        ),
        tags: ['Branches'],
        responses: [
            new OpenApiJsonResponse(resourceName: 'BranchResource'),
        ]
    )]
    public function create(BranchRequest $request): JsonResponse
    {
        $model = new Branch();
        $model->fill($request->validated());

        if ($model->save())
            return ApiResponse::success(BranchResource::make($model));

        return ApiResponse::error("Error on creating a branch");
    }

    #[OA\Put(
        path: '/api/v1/management/branches/{id}',
        description: 'Create a branch route',
        requestBody: new OA\RequestBody(
            description: 'Branch create request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/BranchRequest')
        ),
        tags: ['Branches'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'Branch ID',
                in: 'path',
                required: true
            ),
        ],
        responses: [
            new OpenApiJsonResponse(resourceName: 'BranchResource'),
            new OpenApiNotFoundResponse(),
        ]
    )]
    public function update(Branch $branch, BranchRequest $request): JsonResponse
    {
        $branch->fill($request->validated());

        if ($branch->save()) {
            return ApiResponse::success(BranchResource::make($branch));
        }

        return ApiResponse::error("Error on updating a branch");
    }

    #[OA\Delete(
        path: '/api/v1/management/branches/{id}',
        description: 'Delete branch route',
        tags: ['Branches'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'Branch ID',
                in: 'path',
                required: true
            ),
        ],
        responses: [
            new OpenApiSingleDataResponse(data: 'true', type: 'boolean'),
        ]
    )]
    public function delete(Branch $branch): JsonResponse
    {
        $branch->deleteOrFail();

        return ApiResponse::success(true);
    }
}
