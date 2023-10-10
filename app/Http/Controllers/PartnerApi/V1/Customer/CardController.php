<?php

namespace App\Http\Controllers\PartnerApi\V1\Customer;

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
use App\Http\Requests\Customer\CardFilterRequest;
use App\Http\Requests\Customer\CardRequest;
use App\Http\Requests\Customer\CardsDeleteRequest;
use App\Http\Resources\Customer\CardResource;
use App\Models\Company\Card;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Str;
use OpenApi\Attributes as OA;

class CardController extends Controller
{
    #[OA\Get(
        path: '/api/v1/customer/card',
        description: 'Card route',
        tags: ['Cards'],
        parameters: [
            new OpenApiPageParameter,
            new OpenApiPerPageParameter,
            new OpenApiSearchParameter,
            new OpenApiSortParameter,
        ],
        responses: [
            new OpenApiJsonArrayResponse(resourceName: 'CardResource', withPagination: true, withSortables: true),
        ]
    )]
    public function index(CardFilterRequest $request): AnonymousResourceCollection
    {
        $sortables = ['id', 'pan', 'customer_id', 'balance']; // TODO add all fields, it's just for testing

        return CardResource::collection(
            Card::query()
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
        path: '/api/v1/customer/card/{id}',
        description: 'Card get single route',
        tags: ['Cards'],
        parameters: [
            new OpenApiIdParameter(modelName: 'Card'),
        ],
        responses: [
            new OpenApiJsonResponse(resourceName: 'CardResource'),
            new OpenApiNotFoundResponse,
        ]
    )]
    public function getSingle(Card $card): JsonResponse
    {
        return ApiResponse::success(CardResource::make($card));
    }

    #[OA\Post(
        path: '/api/v1/customer/card',
        description: 'Card create route',
        requestBody: new OA\RequestBody(
            description: 'Card create request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/CardRequest')
        ),
        tags: ['Cards'],
        responses: [
            new OpenApiJsonResponse(resourceName: 'CardResource'),
        ]
    )]
    public function create(CardRequest $request): JsonResponse
    {
        $model = new Card;
        $model->fill($request->validated());

        if ($model->saveOrFail()) {
            return ApiResponse::success(CardResource::make($model));
        }

        return ApiResponse::error('Error on creating Card');
    }

    #[OA\Put(
        path: '/api/v1/customer/card/{id}',
        description: 'Card update route',
        requestBody: new OA\RequestBody(
            description: 'Card create request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/CardRequest')
        ),
        tags: ['Cards'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'Card ID',
                in: 'path',
                required: true
            ),
        ],
        responses: [
            new OpenApiJsonResponse(resourceName: 'CardResource'),
            new OpenApiNotFoundResponse,
        ]
    )]
    public function update(Card $card, CardRequest $request): JsonResponse
    {
        $card->fill($request->validated());

        if ($card->saveOrFail()) {
            return ApiResponse::success(CardResource::make($card));
        }

        return ApiResponse::error('Error on updating Card');
    }

    #[OA\Delete(
        path: '/api/v1/customer/card',
        description: 'Card delete route',
        requestBody: new OA\RequestBody(
            description: 'Card delete request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/CardsDeleteRequest')
        ),
        tags: ['Cards'],
        responses: [
            new OpenApiSingleDataResponse(data: 'true', type: 'boolean'),
        ]
    )]
    public function delete(CardsDeleteRequest $request): JsonResponse
    {
        Card::query()->whereIn('id', $request->ids)->delete();

        return ApiResponse::success(true);
    }
}
