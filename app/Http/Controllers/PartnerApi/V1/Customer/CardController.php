<?php

namespace App\Http\Controllers\PartnerApi\V1\Customer;

use App\Enums\Company\CardStatusEnum;
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
use App\Http\Requests\Customer\AttachCustomerCardRequest;
use App\Http\Requests\Customer\CardFilterRequest;
use App\Http\Requests\Customer\CardRequest;
use App\Http\Requests\Customer\CardsDeleteRequest;
use App\Http\Requests\Customer\CardViewRequest;
use App\Http\Requests\Customer\ConfirmCustomerCardRequest;
use App\Http\Resources\Customer\AttachCustomerCardResource;
use App\Http\Resources\Customer\CardResource;
use App\Models\Company\Card;
use App\Services\PlumService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Str;
use OpenApi\Attributes as OA;

class CardController extends Controller
{
    public function __construct(
        private readonly PlumService $plumService,
    ) {
    }

    #[OA\Get(
        path: '/api/v1/customer-card',
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
        path: '/api/v1/customer-card/{id}',
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
    public function getSingle(int $id, CardViewRequest $request): JsonResponse
    {
        return ApiResponse::success(CardResource::make(Card::findOrFail($id)));
    }

    #[OA\Post(
        path: '/api/v1/customer-card',
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
        $card = new Card;
        $card->fill($request->validated());
        $card->setPan();
        $card->status = CardStatusEnum::INACTIVE;
        if ($card->saveOrFail()) {
            return ApiResponse::success(CardResource::make($card));
        }

        return ApiResponse::error('Error on creating Card');
    }

    #[OA\Put(
        path: '/api/v1/customer-card/{id}',
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
    public function update(int $id, CardRequest $request): JsonResponse
    {
        $card = Card::findOrFail($id);
        $card->fill($request->validated());
        $card->setPan();

        if ($card->saveOrFail()) {
            return ApiResponse::success(CardResource::make($card));
        }

        return ApiResponse::error('Error on updating Card');
    }

    #[OA\Delete(
        path: '/api/v1/customer-card',
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

    #[OA\Post(
        path: '/api/v1/customer-card/{id}/attach',
        description: 'Card attach route',
        requestBody: new OA\RequestBody(
            description: 'Card attach request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/AttachCustomerCardRequest')
        ),
        tags: ['Cards'],
        responses: [
            new OpenApiJsonResponse(resourceName: 'AttachCustomerCardResource'),
        ]
    )]
    public function attach(int $id, AttachCustomerCardRequest $request): JsonResponse
    {
        $card = Card::findOrFail($id);
        $response = $this->plumService->attachCard($card, $request->phone);

        return ApiResponse::success(AttachCustomerCardResource::make($response));
    }

    #[OA\Post(
        path: '/api/v1/customer-card/{id}/confirm',
        description: 'Card confirm route',
        requestBody: new OA\RequestBody(
            description: 'Card confirm request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/AttachCustomerCardRequest')
        ),
        tags: ['Cards'],
        responses: [
            new OpenApiJsonResponse(resourceName: 'CardResource'),
        ]
    )]
    public function confirm(int $id, ConfirmCustomerCardRequest $request): JsonResponse
    {
        $card = Card::findOrFail($id);
        $response = $this->plumService->confirmCard($card, $request->session, $request->otp);
        if ($response === true) {
            return ApiResponse::success(CardResource::make($card));
        }

        return ApiResponse::error($response);
    }
}
