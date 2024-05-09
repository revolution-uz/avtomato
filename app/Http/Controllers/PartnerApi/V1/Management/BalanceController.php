<?php

namespace App\Http\Controllers\PartnerApi\V1\Management;

use App\DTOs\Management\BalanceFundConfirmDTO;
use App\Helpers\Swagger\OpenApiArrayResponse;
use App\Helpers\Swagger\OpenApiDataResponse;
use App\Helpers\Swagger\OpenApiPageParameter;
use App\Helpers\Swagger\OpenApiPerPageParameter;
use App\Helpers\Swagger\OpenApiSearchParameter;
use App\Helpers\Swagger\OpenApiSortParameter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Management\BalanceChargesFilterRequest;
use App\Http\Requests\Management\BalanceFundConfirmRequest;
use App\Http\Requests\Management\BalanceFundFilterRequest;
use App\Http\Requests\Management\BalanceFundRequest;
use App\Http\Resources\Management\BalanceChargesResource;
use App\Http\Resources\Management\BalanceFillingErrorResource;
use App\Http\Resources\Management\BalanceFillingResource;
use App\Http\Resources\Management\BalanceFundResource;
use App\Models\Company\BalanceCharge;
use App\Models\Company\BalanceFund;
use App\Services\AtmosService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Str;
use OpenApi\Attributes as OA;
use Spatie\FlareClient\Http\Exceptions\BadResponse;

class BalanceController extends Controller
{
    public function __construct(
        private readonly AtmosService $atmosCardService
    ) {
        $this->atmosCardService->setRevolutionCredentials();
    }

    #[OA\Get(
        path: '/api/v1/management/balance/funds',
        description: 'Balance funds list',
        tags: ['Balance'],
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
    public function funds(BalanceFundFilterRequest $request): AnonymousResourceCollection
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

    #[OA\Get(
        path: '/api/v1/management/balance/charges',
        description: 'Balance charges list',
        tags: ['Balance'],
        parameters: [
            new OpenApiPageParameter,
            new OpenApiPerPageParameter,
            new OpenApiSearchParameter,
            new OpenApiSortParameter,
        ],
        responses: [
            new OpenApiArrayResponse(resourceName: 'BalanceChargesResource', withPagination: true, withSortables: true),
        ]
    )]
    public function charges(BalanceChargesFilterRequest $request): AnonymousResourceCollection
    {
        $sortables = ['id', 'feature_id', 'amount']; // TODO add all fields, it's just for testing

        return BalanceChargesResource::collection(
            BalanceCharge::query()
                ->when(
                    $request->search,
                    fn (Builder $q) => $q
                        ->where('feature_id', $request->search)

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
        path: '/api/v1/management/balance/fill',
        description: 'Funding customer balance',
        requestBody: new OA\RequestBody(
            description: 'Funding balance request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/BalanceFundRequest')
        ),
        tags: ['Balance'],

        responses: [
            new OpenApiDataResponse(resourceName: 'BalanceFillingResource'),
            new OpenApiDataResponse(resourceName: 'BalanceFillingErrorResource', response: 401),
        ]
    )]
    public function fill(BalanceFundRequest $request): BalanceFillingResource|BalanceFillingErrorResource
    {
        $response = $this->atmosCardService->attachCard($request->number, $request->expiry);

        if ($response->result->isSucces()) {
            return new BalanceFillingResource($response);
        }

        return new BalanceFillingErrorResource($response);
    }

    #[OA\Post(
        path: '/api/v1/management/balance/confirm-payment',
        description: 'Confirming payment of funding user balance',
        requestBody: new OA\RequestBody(
            description: 'User payment request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/BalanceFundConfirmRequest')
        ),
        tags: ['Balance'],

        responses: [
            new OpenApiDataResponse(resourceName: 'BalanceFundResource'),
        ]
    )]
    public function confirmPayment(BalanceFundConfirmRequest $request): BadResponse|BalanceFundResource
    {

        $result = $this->atmosCardService->fundBalance(BalanceFundConfirmDTO::from($request->toArray()));

        return $result != null ? new BalanceFundResource($result) : new BadResponse('Error on confirming payment');
    }
}
