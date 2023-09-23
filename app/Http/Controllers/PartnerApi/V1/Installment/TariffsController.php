<?php

namespace App\Http\Controllers\PartnerApi\V1\Installment;

use App\Helpers\Swagger\OpenApiJsonArrayResponse;
use App\Helpers\Swagger\OpenApiJsonResponse;
use App\Helpers\Swagger\OpenApiNotFoundResponse;
use App\Helpers\Swagger\OpenApiSingleDataResponse;
use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Requests\TariffRequest;
use App\Http\Resources\TariffResource;
use App\Models\Company\Tariff;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class TariffsController extends Controller
{
    #[OA\Get(
        path: '/api/v1/installment/tariffs',
        description: 'Tariffs route',
        tags: ['Tariffs'],
        responses: [
            new OpenApiJsonArrayResponse(resourceName: 'TariffResource'),
        ]
    )]
    public function index(): JsonResponse
    {
        return ApiResponse::success(TariffResource::collection(Tariff::all()));
    }

    #[OA\Get(
        path: '/api/v1/installment/tariffs/{id}',
        description: 'Tariffs get single route',
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
            new OpenApiNotFoundResponse(),
        ]
    )]
    public function getSingle(Tariff $tariff): JsonResponse
    {
        return ApiResponse::success(TariffResource::make($tariff));
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
        $model = new Tariff();
        $model->fill($request->validated());

        if ($model->save())
            return ApiResponse::success(TariffResource::make($model));

        return ApiResponse::error("Error on creating Tariffs");
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
            new OpenApiNotFoundResponse(),
        ]
    )]
    public function update(Tariff $tariff, TariffRequest $request): JsonResponse
    {
        $tariff->fill($request->validated());

        if ($tariff->save()) {
            return ApiResponse::success(TariffResource::make($tariff));
        }

        return ApiResponse::error("Error on updating Tariffs");
    }

    #[OA\Delete(
        path: '/api/v1/installment/tariffs/{id}',
        description: 'Tariffs delete route',
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
            new OpenApiSingleDataResponse(data: 'true', type: 'boolean'),
        ]
    )]
    public function delete(Tariff $tariff): JsonResponse
    {
        $tariff->deleteOrFail();

        return ApiResponse::success(true);
    }
}
