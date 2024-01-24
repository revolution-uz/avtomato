<?php

namespace App\Http\Controllers\PartnerApi\V1\Management;

use App\Helpers\Swagger\OpenApiDataResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Management\ConfirmSubscriptionPaymentRequest;
use App\Http\Requests\Management\FundCustomerBalanceRequest;
use App\Http\Resources\Management\SubscriptionConfirmPaymentResource;
use App\Http\Resources\Management\SubscriptionFundBalanceResource;
use App\Services\PlumService;
use OpenApi\Attributes as OA;

class SubscriptionController extends Controller
{
    public function __construct(
        private readonly PlumService $plumService
    ) {
    }

    #[OA\Post(
        path: '/api/v1/customer/{id}/fund-balance',
        description: 'Funding customer balance',
        requestBody: new OA\RequestBody(
            description: 'Funding balance request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/FundCustomerBalanceRequest')
        ),
        tags: ['Subscription'],

        responses: [
            new OpenApiDataResponse(resourceName: 'SubscriptionFundBalanceResource'),
        ]
    )]
    public function fundBalance(FundCustomerBalanceRequest $request): SubscriptionFundBalanceResource
    {
        $data = [
            'amount' => $request->amount,
            'cardNumber' => $request->cardNumber,
            'expireDate' => $request->expireDate,
        ];

        // Check if $request->transactionData is not null before adding it to the data array
        if ($request->has('transactionData') && $request->transactionData !== null) {
            $data['transactionData'] = $request->transactionData;
        }
        $response = $this->plumService->fundBalance($data);

        return new SubscriptionFundBalanceResource($response);
    }

    #[OA\Post(
        path: '/api/v1/customer/{id}/fund-balance/confirm-payment',
        description: 'Confirming payment of funding customer balance',
        requestBody: new OA\RequestBody(
            description: 'Confirm payment request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/FundCustomerBalanceRequest')
        ),
        tags: ['Subscription'],

        responses: [
            new OpenApiDataResponse(resourceName: 'SubscriptionConfirmPaymentResource'),
        ]
    )]
    public function confirmPayment(ConfirmSubscriptionPaymentRequest $request): SubscriptionConfirmPaymentResource
    {
        $data = [
            'session' => $request->session,
            'otp' => $request->otp,
        ];

        $response = $this->plumService->confirmPayment($data);

        return new SubscriptionConfirmPaymentResource($response);
    }
}
