<?php

namespace App\Http\Controllers\PartnerApi;

use App\Http\Controllers\Controller;
use App\Services\LoyaltyProgram\LoyaltyProgramOrderService;
use Illuminate\Http\Request;

class LoyaltyProgramDiscountController extends Controller
{
    protected $channel = 'loyalty-program';
    protected $type = 'discount-pos-system';

    public function __construct(readonly LoyaltyProgramOrderService $loyaltyProgramOrderService) {}

    public function createOrder(Request $request)
    {
        $this->loyaltyProgramOrderService->createOrder($request->input('items'), $request->input('qr'));
        $response = ['success' => true, 'message' => 'Shops processed successfully'];
        $this->logging($request, $response, $this->channel, $this->type, 'postShops');

        return $this->successResponse($response);
    }

    public function completeOrder(Request $request)
    {
        $result = $this->loyaltyProgramOrderService->confirmOrder($request->input('transaction_id'));
        $response = ['success' => $result, 'message' => $result ? 'Transaction confirmed successfully' : 'Transaction not found'];
        $this->logging($request, $response, $this->channel, $this->type, 'completeOrder');

        return $this->successResponse($response);
    }

    public function cancelOrder(Request $request)
    {
        $result = $this->loyaltyProgramOrderService->cancelOrder($request->input('transaction_id'));
        $response = ['success' => $result, 'message' => $result ? 'Transaction canceled successfully' : 'Transaction not found'];
        $this->logging($request, $response, $this->channel, $this->type, 'cancelOrder');

        return $this->successResponse($response);
    }

    public function successResponse($data)
    {
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function failedResponse($message)
    {
        return response()->json(['success' => true, 'message' => $message]);
    }
}
