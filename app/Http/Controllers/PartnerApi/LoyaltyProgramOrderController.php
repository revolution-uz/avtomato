<?php

namespace App\Http\Controllers\PartnerApi;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoyaltyProgram\CompleteOrderRequest;
use App\Http\Requests\LoyaltyProgram\CreateOrderRequest;
use App\Services\LoyaltyProgram\LoyaltyProgramOrderService;
use Exception;

class LoyaltyProgramOrderController extends Controller
{
    protected $channel = 'loyalty-program';
    protected $type = 'discount-pos-system';

    public function __construct(readonly private LoyaltyProgramOrderService $loyaltyProgramOrderService) {}

    public function createOrder(CreateOrderRequest $request)
    {
        try {
            $data = $this->loyaltyProgramOrderService->createOrder($request->items, $request->shop_id, $request->qr);
            $response = $this->prepareSuccessResponse($data);
        } catch (Exception $e) {
            $response = $this->prepareFailedResponse($e->getMessage());
        }

        $this->logging($request, $response, $this->channel, $this->type, 'createOrder');

        return $this->jsonResponse($response);
    }

    public function completeOrder(CompleteOrderRequest $request)
    {
        try {
            $data = $this->loyaltyProgramOrderService->confirmOrder($request->order_id);
            $response = $this->prepareSuccessResponse($data);
        } catch (Exception $e) {
            $response = $this->prepareFailedResponse($e->getMessage());
        }

        $this->logging($request, $response, $this->channel, $this->type, 'completeOrder');

        return $this->jsonResponse($response);
    }

    public function cancelOrder(CompleteOrderRequest $request)
    {
        try {
            $data = $this->loyaltyProgramOrderService->cancelOrder($request->order_id);
            $response = $this->prepareSuccessResponse($data);

        } catch (Exception $e) {
            $response = $this->prepareFailedResponse($e->getMessage());
        }

        $this->logging($request, $response, $this->channel, $this->type, 'cancelOrder');

        return $this->jsonResponse($response);
    }

    public function jsonResponse($response)
    {
        return response()->json($response);
    }

    public function prepareFailedResponse($message)
    {
        return [
            'status' => false, 'result' => null, 'error' => [
                'message' => $message,
            ],
        ];
    }

    private function prepareSuccessResponse($result)
    {
        return ['status' => true, 'result' => $result, 'error' => null];
    }
}
