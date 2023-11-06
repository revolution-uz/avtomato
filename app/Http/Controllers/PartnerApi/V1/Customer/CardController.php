<?php

namespace App\Http\Controllers\PartnerApi\V1\Customer;

use App\Enums\Company\CardStatusEnum;
use App\Helpers\Swagger\OpenApiArrayResponse;
use App\Helpers\Swagger\OpenApiDataResponse;
use App\Helpers\Swagger\OpenApiIdParameter;
use App\Helpers\Swagger\OpenApiNotFoundResponse;
use App\Helpers\Swagger\OpenApiPageParameter;
use App\Helpers\Swagger\OpenApiPerPageParameter;
use App\Helpers\Swagger\OpenApiSearchParameter;
use App\Helpers\Swagger\OpenApiSortParameter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\CardFilterRequest;
use App\Http\Requests\Customer\CardRequest;
use App\Http\Requests\Customer\CardsDeleteRequest;
use App\Http\Requests\Customer\CardViewRequest;
use App\Http\Requests\Customer\ConfirmCustomerCardRequest;
use App\Http\Resources\Customer\AttachCustomerCardResource;
use App\Http\Resources\Customer\CardResource;
use App\Http\Resources\StatusResource;
use App\Models\Company\Card;
use App\Services\PlumService;
use Illuminate\Database\Eloquent\Builder;
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
            new OpenApiArrayResponse(resourceName: 'CardResource', withPagination: true, withSortables: true),
        ]
    )]
    public function index(CardFilterRequest $request): AnonymousResourceCollection
    {
        $sortables = ['id', 'pan', 'phone', 'customer_id', 'balance']; // TODO add all fields, it's just for testing

        return CardResource::collection(
            Card::query()
                ->when(
                    $request->search,
                    fn (Builder $q) => $q
                        ->where('name', 'ILIKE', '%' . $request->search . '%')
                        ->where('phone', 'ILIKE', '%' . $request->search . '%')
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
        path: '/api/v1/customer-card/{id}',
        description: 'Card get single route',
        tags: ['Cards'],
        parameters: [
            new OpenApiIdParameter(modelName: 'Card'),
        ],
        responses: [
            new OpenApiDataResponse(resourceName: 'CardResource'),
            new OpenApiNotFoundResponse,
        ]
    )]
    public function getSingle(int $id, CardViewRequest $request): CardResource
    {
        return new CardResource(Card::findOrFail($id));
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
            new OpenApiDataResponse(resourceName: 'AttachCustomerCardResource'),
        ]
    )]
    public function create(CardRequest $request): AttachCustomerCardResource
    {
        $card = new Card;
        $card->fill($request->validated());
        $card->setPan();
        $card->status = CardStatusEnum::INACTIVE;
        $card->saveOrFail();

        return AttachCustomerCardResource::make($this->plumService->attachCard($card), new CardResource($card));
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
            new OpenApiDataResponse(resourceName: 'CardResource'),
            new OpenApiNotFoundResponse,
        ]
    )]
    public function update(int $id, CardRequest $request): CardResource
    {
        $card = Card::findOrFail($id);
        $card->fill($request->validated());
        $card->setPan();
        $card->saveOrFail();

        return new CardResource($card);
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
            new OpenApiDataResponse('StatusResource'),
        ]
    )]
    public function delete(CardsDeleteRequest $request): StatusResource
    {
        Card::query()->whereIn('id', $request->ids)->delete();

        return new StatusResource(true);
    }

    #[OA\Post(
        path: '/api/v1/customer-card/{id}/confirm',
        description: 'Card confirm route',
        requestBody: new OA\RequestBody(
            description: 'Card confirm request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/ConfirmCustomerCardRequest')
        ),
        tags: ['Cards'],
        responses: [
            new OpenApiDataResponse(resourceName: 'CardResource'),
        ]
    )]
    public function confirm(int $id, ConfirmCustomerCardRequest $request): CardResource
    {
        $card = Card::findOrFail($id);
        $this->plumService->confirmCard($card, $request->session, $request->otp);

        return CardResource::make($card);
    }
}
