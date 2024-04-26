<?php

namespace App\Http\Controllers\PartnerApi\V1\Management;

use App\DTOs\Atmos\Card\AtmosAttachCardRequestDTO;
use App\DTOs\Management\BalanceFundConfirmDTO;
use App\Helpers\Swagger\OpenApiArrayResponse;
use App\Helpers\Swagger\OpenApiDataResponse;
use App\Helpers\Swagger\OpenApiPageParameter;
use App\Helpers\Swagger\OpenApiPerPageParameter;
use App\Helpers\Swagger\OpenApiSearchParameter;
use App\Helpers\Swagger\OpenApiSortParameter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Management\BalanceFundConfirmRequest;
use App\Http\Requests\Management\BalanceFundFilterRequest;
use App\Http\Requests\Management\BalanceFundRequest;
use App\Http\Resources\Management\BalanceFundResource;
use App\Http\Resources\Management\SubscriptionFundBalanceErrorResource;
use App\Http\Resources\Management\SubscriptionFundBalanceResource;
use App\Models\Company\BalanceFund;
use App\Services\AtmosCardService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Str;
use OpenApi\Attributes as OA;

class SubscriptionController extends Controller
{
    public function __construct(
        private readonly AtmosCardService $atmosCardService
    ) {
    }

    #[OA\Get(
        path: '/api/v1/subscription',
        description: 'Subscription route',
        tags: ['Subscription'],
        parameters: [
            new OpenApiPageParameter,
            new OpenApiPerPageParameter,
            new OpenApiSearchParameter,
            new OpenApiSortParameter,
        ],
        responses: [
            new OpenApiArrayResponse(resourceName: 'BalanceFundResource', withPagination: true, withSortables: true),
        ]
    )]
    public function index(BalanceFundFilterRequest $request): AnonymousResourceCollection
    {
        $sortables = ['id', 'amount', 'phone_number', 'card_number']; // TODO add all fields, it's just for testing

        return BalanceFundResource::collection(
            BalanceFund::query()
                ->when(
                    $request->search,
                    fn (Builder $q) => $q
                        ->where('phone_number', 'ILIKE', '%' . $request->search . '%')
                        ->where('card_number', 'ILIKE', '%' . $request->search . '%')

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

    #[OA\Post(
        path: '/api/v1/subscription/fund-balance',
        description: 'Funding customer balance',
        requestBody: new OA\RequestBody(
            description: 'Funding balance request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/BalanceFundRequest')
        ),
        tags: ['Subscription'],

        responses: [
            new OpenApiDataResponse(resourceName: 'SubscriptionFundBalanceResource'),
        ]
    )]
    public function fundBalance(BalanceFundRequest $request): SubscriptionFundBalanceResource|SubscriptionFundBalanceErrorResource
    {
        $response = $this->atmosCardService->attachCard(AtmosAttachCardRequestDTO::from($request->toArray()));

        if ($response->result->isSucces()) {
            return new SubscriptionFundBalanceResource($response);
        }

        return new SubscriptionFundBalanceErrorResource($response);
    }

    #[OA\Post(
        path: '/api/v1/subscription/confirm-payment',
        description: 'Confirming payment of funding user balance',
        requestBody: new OA\RequestBody(
            description: 'User payment request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/ConfirmSubscriptionPaymentRequest')
        ),
        tags: ['Subscription'],

        responses: [
            new OpenApiDataResponse(resourceName: 'SubscriptionConfirmPaymentResource'),
        ]
    )]
    public function confirmPayment(BalanceFundConfirmRequest $request): BalanceFundResource
    {

        return new BalanceFundResource($this->atmosCardService->fundBalance(BalanceFundConfirmDTO::from($request->toArray())));
    }
}
