<?php

namespace App\Http\Controllers\PartnerApi\V1\Installment;

use App\Helpers\Swagger\OpenApiArrayResponse;
use App\Helpers\Swagger\OpenApiDataResponse;
use App\Helpers\Swagger\OpenApiIdParameter;
use App\Helpers\Swagger\OpenApiNotFoundResponse;
use App\Helpers\Swagger\OpenApiPageParameter;
use App\Helpers\Swagger\OpenApiPerPageParameter;
use App\Helpers\Swagger\OpenApiSearchParameter;
use App\Helpers\Swagger\OpenApiSortParameter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Installment\TariffRequest;
use App\Http\Requests\Installment\TariffsDeleteRequest;
use App\Http\Requests\Installment\TariffsFilterRequest;
use App\Http\Requests\Installment\TariffViewRequest;
use App\Http\Resources\Installment\TariffResource;
use App\Http\Resources\StatusResource;
use App\Models\Company\Tariff;
use Illuminate\Database\Eloquent\Builder;
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
            new OpenApiArrayResponse(resourceName: 'TariffResource', withPagination: true, withSortables: true),
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
        )->additional(['sortables' => $sortables]);
    }

    #[OA\Get(
        path: '/api/v1/installment/tariffs/{id}',
        description: 'Tariffs get single route',
        tags: ['Tariffs'],
        parameters: [
            new OpenApiIdParameter(modelName: 'Tariff'),
        ],
        responses: [
            new OpenApiDataResponse(resourceName: 'TariffResource'),
            new OpenApiNotFoundResponse,
        ]
    )]
    public function getSingle(int $id, TariffViewRequest $request): TariffResource
    {
        return new TariffResource(Tariff::findOrFail($id));
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
            new OpenApiDataResponse(resourceName: 'TariffResource', response: '201'),
        ]
    )]
    public function create(TariffRequest $request): TariffResource
    {
        $model = new Tariff;
        $model->fill($request->validated());
        $model->saveOrFail();

        return new TariffResource($model);
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
            new OpenApiDataResponse(resourceName: 'TariffResource'),
            new OpenApiNotFoundResponse,
        ]
    )]
    public function update(int $id, TariffRequest $request): TariffResource
    {
        /** @var Tariff $tariff */
        $tariff = Tariff::findOrFail($id);
        $tariff->fill($request->validated());
        $tariff->saveOrFail();

        return new TariffResource($tariff);
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
            new OpenApiDataResponse(resourceName: 'StatusResource'),
        ]
    )]
    public function delete(TariffsDeleteRequest $request): StatusResource
    {
        Tariff::query()->whereIn('id', $request->ids)->delete();

        return new StatusResource(true);
    }
}
