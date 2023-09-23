<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Requests\TariffsRequest;
use App\Http\Resources\TariffsResource;
use App\Models\Company\Tariff;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class TariffsController extends Controller
{
    #[OA\Get(
        path: '/api/v1/tariffs',
        operationId: 'tariffs',
        description: 'Tariffs route',
        summary: 'Tariffs route',
        tags: ['Tariffs'],
        responses: [
            new OA\Response(
                response: '200',
                description: 'Success',
                content: new OA\JsonContent(
                    example: [
                        'success' => true,
                        'message' => null,
                        'data' => [
                            [
                                'id' => 1,
                                'name' => 'Tariff 1',
                                'is_annual' => true,
                                'percent' => 10,
                                'min_amount' => 1000,
                                'max_amount' => 100000,
                                'min_initial_fee' => 0,
                                'max_initial_fee' => 10000,
                            ],
                            [
                                'id' => 2,
                                'name' => 'Tariff 2',
                                'is_annual' => false,
                                'percent' => 15,
                                'min_amount' => 1000,
                                'max_amount' => 100000,
                                'min_initial_fee' => 0,
                                'max_initial_fee' => 10000,
                            ],
                        ],
                    ]
                )
            ),
        ]
    )]
    public function index(): JsonResponse
    {
        return ApiResponse::success(TariffsResource::collection(Tariff::all()));
    }

    #[OA\Get(
        path: '/api/v1/tariffs/{id}',
        operationId: 'tariffsGetSingle',
        description: 'Tariffs get single route',
        summary: 'Tariffs get single route',
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
            new OA\Response(
                response: '200',
                description: 'Success',
                content: new OA\JsonContent(
                    example: [
                        'success' => true,
                        'message' => null,
                        'data' => [
                            'id' => 1,
                            'name' => 'Tariff 1',
                            'is_annual' => true,
                            'percent' => 10,
                            'min_amount' => 1000,
                            'max_amount' => 100000,
                            'min_initial_fee' => 0,
                            'max_initial_fee' => 10000,
                        ],
                    ]
                )
            ),
        ]
    )]
    public function getSingle(Tariff $tariff): JsonResponse
    {
        return ApiResponse::success(TariffsResource::make($tariff));
    }

    #[OA\Post(
        path: '/api/v1/tariffs/create',
        operationId: 'tariffsCreate',
        description: 'Tariffs create route',
        summary: 'Tariffs create route',
        requestBody: new OA\RequestBody(
            description: 'Tariffs create request body',
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Tariff 1'),
                    new OA\Property(property: 'is_annual', type: 'boolean', example: true),
                    new OA\Property(property: 'percent', type: 'number', example: 10),
                    new OA\Property(property: 'min_amount', type: 'number', example: 1000),
                    new OA\Property(property: 'max_amount', type: 'number', example: 100000),
                    new OA\Property(property: 'min_initial_fee', type: 'number', example: 0),
                    new OA\Property(property: 'max_initial_fee', type: 'number', example: 10000),
                ]
            )
        ),
        tags: ['Tariffs'],
        responses: [
            new OA\Response(
                response: '200',
                description: 'Success',
                content: new OA\JsonContent(
                    example: [
                        'success' => true,
                        'message' => null,
                        'data' => [
                            'id' => 1,
                            'name' => 'Tariff 1',
                            'is_annual' => true,
                            'percent' => 10,
                            'min_amount' => 1000,
                            'max_amount' => 100000,
                            'min_initial_fee' => 0,
                            'max_initial_fee' => 10000,
                        ],
                    ]
                )
            ),
            new OA\Response(
                response: '404',
                description: 'Not found',
                content: new OA\JsonContent(
                    example: [
                        'success' => true,
                        'message' => 'Not found',
                        'data' => null,
                    ]
                )
            ),
        ]
    )]
    public function create(TariffsRequest $request): JsonResponse
    {
        $model = new Tariff();
        $model->name = $request->name;
        $model->is_annual = $request->is_annual;
        $model->percent = $request->percent;
        $model->min_amount = $request->min_amount;
        $model->max_amount = $request->max_amount;
        $model->min_initial_fee = $request->min_initial_fee;
        $model->max_initial_fee = $request->max_initial_fee;
        if ($model->save())
            return ApiResponse::success(TariffsResource::make($model));
        return ApiResponse::error("Error on creating Tariffs");
    }

    #[OA\Post(
        path: '/api/v1/tariffs/{id}/update',
        operationId: 'tariffsUpdate',
        description: 'Tariffs create route',
        summary: 'Tariffs create route',
        requestBody: new OA\RequestBody(
            description: 'Tariffs create request body',
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Tariff 1'),
                    new OA\Property(property: 'is_annual', type: 'boolean', example: true),
                    new OA\Property(property: 'percent', type: 'number', example: 10),
                    new OA\Property(property: 'min_amount', type: 'number', example: 1000),
                    new OA\Property(property: 'max_amount', type: 'number', example: 100000),
                    new OA\Property(property: 'min_initial_fee', type: 'number', example: 0),
                    new OA\Property(property: 'max_initial_fee', type: 'number', example: 10000),
                ]
            )
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
            new OA\Response(
                response: '200',
                description: 'Success',
                content: new OA\JsonContent(
                    example: [
                        'success' => true,
                        'message' => null,
                        'data' => [
                            'id' => 1,
                            'name' => 'Tariff 1',
                            'is_annual' => true,
                            'percent' => 10,
                            'min_amount' => 1000,
                            'max_amount' => 100000,
                            'min_initial_fee' => 0,
                            'max_initial_fee' => 10000,
                        ],
                    ]
                )
            ),
            new OA\Response(
                response: '404',
                description: 'Not found',
                content: new OA\JsonContent(
                    example: [
                        'success' => true,
                        'message' => 'Not found',
                        'data' => null,
                    ]
                )
            ),
        ]
    )]
    public function update(Tariff $tariff, TariffsRequest $request): JsonResponse
    {
        $tariff->name = $request->name;
        $tariff->is_annual = $request->is_annual;
        $tariff->percent = $request->percent;
        $tariff->min_amount = $request->min_amount;
        $tariff->max_amount = $request->max_amount;
        $tariff->min_initial_fee = $request->min_initial_fee;
        $tariff->max_initial_fee = $request->max_initial_fee;
        if ($tariff->save()) {
            return ApiResponse::success(TariffsResource::make($tariff));
        }
        return ApiResponse::error("Error on updating Tariffs");
    }

    public function delete(Tariff $tariffs): JsonResponse
    {
        return ApiResponse::success($tariffs->delete());
    }
}
