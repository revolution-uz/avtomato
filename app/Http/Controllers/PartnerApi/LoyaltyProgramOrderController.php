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
            $this->loyaltyProgramOrderService->createOrder($request->items, $request->qr);
            $response = ['success' => true, 'message' => 'Order processed successfully'];

        } catch (Exception $e) {
            $response = ['success' => false, 'message' => $e->getMessage()];
        }

        $this->logging($request, $response, $this->channel, $this->type, 'createOrder');

        return $this->successResponse($response);
    }

    public function completeOrder(CompleteOrderRequest $request)
    {
        try {
            $this->loyaltyProgramOrderService->confirmOrder($request->order_id);
            $response = ['success' => true, 'message' => 'Order completed successfully'];

        } catch (Exception $e) {
            $response = ['success' => false, 'message' => $e->getMessage()];
        }

        $this->logging($request, $response, $this->channel, $this->type, 'completeOrder');

        return $this->successResponse($response);
    }

    public function cancelOrder(CompleteOrderRequest $request)
    {
        try {
            $this->loyaltyProgramOrderService->cancelOrder($request->order_id);
            $response = ['success' => true, 'message' => 'Order canceled successfully'];

        } catch (Exception $e) {
            $response = ['success' => false, 'message' => $e->getMessage()];
        }

        $this->logging($request, $response, $this->channel, $this->type, 'cancelOrder');

        return $this->successResponse($response);
    }

    public function successResponse($data)
    {
        return response()->json(['success' => true, 'data' => $data]);
    }
}
