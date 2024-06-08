<?php

namespace App\Http\Controllers\PartnerApi\V1\Management;

use App\Helpers\Swagger\OpenApiArrayResponse;
use App\Helpers\Swagger\OpenApiDataResponse;
use App\Helpers\Swagger\OpenApiIdParameter;
use App\Helpers\Swagger\OpenApiNotFoundResponse;
use App\Helpers\Swagger\OpenApiPageParameter;
use App\Helpers\Swagger\OpenApiPerPageParameter;
use App\Helpers\Swagger\OpenApiSearchParameter;
use App\Helpers\Swagger\OpenApiSortParameter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Management\BranchesDeleteRequest;
use App\Http\Requests\Management\BranchesFilterRequest;
use App\Http\Requests\Management\BranchRequest;
use App\Http\Requests\Management\BranchViewRequest;
use App\Http\Resources\Management\BranchResource;
use App\Http\Resources\StatusResource;
use App\Models\Company\Branch;
use Illuminate\Database\Eloquent\Builder;
use OpenApi\Attributes as OA;

class BranchController extends Controller
{
    #[OA\Get(
        path: '/api/v1/management/branches',
        description: 'Get branches list route',
        tags: ['Branches'],
        parameters: [
            new OpenApiPageParameter,
            new OpenApiPerPageParameter,
            new OpenApiSearchParameter,
            new OpenApiSortParameter,
        ],
        responses: [
            new OpenApiArrayResponse(resourceName: 'BranchResource', withPagination: true, withSortables: true),
        ]
    )]
    public function index(BranchesFilterRequest $request)
    {
        $request->sortables = ['id', 'name', 'address', 'inn', 'created_at', 'updated_at'];

        return BranchResource::collection(
            Branch::query()
                ->when(
                    $request->search,
                    fn (Builder $q) => $q
                        ->where('name', 'ILIKE', '%' . $request->search . '%')
                        ->where('address', 'ILIKE', '%' . $request->search . '%')
                        ->where('inn', 'ILIKE', '%' . $request->search . '%')
                )
                ->when($request->isSortable(), function (Builder $q) use ($request) {
                    $q->orderBy($request->sortField(), $request->sortDirection());
                })
                ->paginate($request->per_page)
        )->additional(['sortables' => $request->sortables]);
    }

    #[OA\Get(
        path: '/api/v1/management/branches/{id}',
        description: 'Get single branch route',
        tags: ['Branches'],
        parameters: [
            new OpenApiIdParameter(modelName: 'Branch'),
        ],
        responses: [
            new OpenApiDataResponse(resourceName: 'BranchResource'),
            new OpenApiNotFoundResponse,
        ]
    )]
    public function getSingle(int $id, BranchViewRequest $request): BranchResource
    {
        return new BranchResource(Branch::findOrFail($id));
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
            new OpenApiDataResponse(resourceName: 'BranchResource', response: '201'),
        ]
    )]
    public function create(BranchRequest $request): BranchResource
    {
        $model = new Branch;
        $model->fill($request->validated());
        $model->saveOrFail();

        return new BranchResource($model);
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
            new OpenApiDataResponse(resourceName: 'BranchResource'),
            new OpenApiNotFoundResponse,
        ]
    )]
    public function update(int $id, BranchRequest $request): BranchResource
    {
        $branch = Branch::findOrFail($id);
        $branch->fill($request->validated());
        $branch->saveOrFail();

        return new BranchResource($branch);
    }

    #[OA\Delete(
        path: '/api/v1/management/branches',
        description: 'Delete branch route',
        requestBody: new OA\RequestBody(
            description: 'Branches delete request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/BranchesDeleteRequest')
        ),
        tags: ['Branches'],
        responses: [
            new OpenApiDataResponse(resourceName: 'StatusResource'),
        ]
    )]
    public function delete(BranchesDeleteRequest $request): StatusResource
    {
        Branch::query()->whereIn('id', $request->ids)->delete();

        return new StatusResource(true);
    }
}
