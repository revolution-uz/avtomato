<?php

namespace App\Http\Controllers\PartnerApi\V1\Installment;

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
use App\Http\Requests\Installment\TariffRequest;
use App\Http\Requests\Installment\TariffsDeleteRequest;
use App\Http\Requests\Installment\TariffsFilterRequest;
use App\Http\Requests\Installment\TariffViewRequest;
use App\Http\Resources\Installment\TariffResource;
use App\Models\Company\Tariff;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Str;
use OpenApi\Attributes as OA;

class TariffsController extends Controller
{
    #[OA\Get(
        path: '/api/v1/installment/tariffs',
        description: 'Tariffs route',
        tags: ['Tariffs'],
        parameters: [
            new OpenApiPageParameter,
            new OpenApiPerPageParameter,
            new OpenApiSearchParameter,
            new OpenApiSortParameter,
        ],
        responses: [
            new OpenApiJsonArrayResponse(resourceName: 'TariffResource', withPagination: true, withSortables: true),
        ]
    )]
    public function index(TariffsFilterRequest $request): AnonymousResourceCollection
    {
        $sortables = ['id', 'name', 'is_annual', 'percent']; // TODO add all fields, it's just for testing

        return TariffResource::collection(
            Tariff::query()
                ->when(
                    $request->search,
                    fn (Builder $q) => $q->where('name', 'ILIKE', '%' . $request->search . '%')
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

    #[OA\Get(
        path: '/api/v1/installment/tariffs/{id}',
        description: 'Tariffs get single route',
        tags: ['Tariffs'],
        parameters: [
            new OpenApiIdParameter(modelName: 'Tariff'),
        ],
        responses: [
            new OpenApiJsonResponse(resourceName: 'TariffResource'),
            new OpenApiNotFoundResponse,
        ]
    )]
    public function getSingle(int $id, TariffViewRequest $request): JsonResponse
    {
        return ApiResponse::success(TariffResource::make(Tariff::findOrFail($id)));
    }

    #[OA\Post(
        path: '/api/v1/installment/tariffs',
        description: 'Tariffs create route',
        requestBody: new OA\RequestBody(
            description: 'Tariffs create request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/TariffRequest')
        ),
        tags: ['Tariffs'],
        responses: [
            new OpenApiJsonResponse(resourceName: 'TariffResource'),
        ]
    )]
    public function create(TariffRequest $request): JsonResponse
    {
        $model = new Tariff;
        $model->fill($request->validated());

        if ($model->save()) {
            return ApiResponse::success(TariffResource::make($model));
        }

        return ApiResponse::error('Error on creating Tariffs');
    }

    #[OA\Put(
        path: '/api/v1/installment/tariffs/{id}',
        description: 'Tariffs create route',
        requestBody: new OA\RequestBody(
            description: 'Tariffs create request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/TariffRequest')
        ),
        tags: ['Tariffs'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'Tariff ID',
                in: 'path',
                required: true
            ),
        ],
        responses: [
            new OpenApiJsonResponse(resourceName: 'TariffResource'),
            new OpenApiNotFoundResponse,
        ]
    )]
    public function update(int $id, TariffRequest $request): JsonResponse
    {
        $tariff = Tariff::findOrFail($id);
        $tariff->fill($request->validated());

        if ($tariff->save()) {
            return ApiResponse::success(TariffResource::make($tariff));
        }

        return ApiResponse::error('Error on updating Tariffs');
    }

    #[OA\Delete(
        path: '/api/v1/installment/tariffs',
        description: 'Tariffs delete route',
        requestBody: new OA\RequestBody(
            description: 'Tariffs delete request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/TariffsDeleteRequest')
        ),
        tags: ['Tariffs'],
        responses: [
            new OpenApiSingleDataResponse(data: 'true', type: 'boolean'),
        ]
    )]
    public function delete(TariffsDeleteRequest $request): JsonResponse
    {
        Tariff::query()->whereIn('id', $request->ids)->delete();

        return ApiResponse::success(true);
    }
}
