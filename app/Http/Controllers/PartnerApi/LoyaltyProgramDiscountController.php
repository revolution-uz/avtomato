<?php

namespace App\Http\Controllers\PartnerApi;

use App\DTOs\LoyaltyProgram\CartDTO;
use App\Enums\Company\LoyaltyProgramm\OrderStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\Company\LoyaltyProgram\Order;
use Illuminate\Http\Request;

class LoyaltyProgramDiscountController extends Controller
{
    protected $channel = 'loyalty-program';
    protected $type = 'discount-pos-system';

    public function createOrder(Request $request)
    {
        $cart = collect($request->input('cart'))->map(function ($cartData) {
            return CartDTO::from($cartData);
        });

        $response = ['success' => true, 'message' => 'Shops processed successfully'];
        $this->logging($request, $response, $this->channel, $this->type, 'postShops');

        return $this->successResponse($response);
    }

    public function completeOrder(Request $request)
    {
        $transactionId = $request->input('transaction_id');
        $order = Order::where('id', $transactionId)->first();
        if (! $order) {
            return $this->failedResponse('Transaction not found');
        }

        $order->status = OrderStatusEnum::COMPLETED;
        $order->save();

        $response = ['success' => true, 'message' => 'Transaction confirmed successfully'];
        $this->logging($request, $response, $this->channel, $this->type, 'completeOrder');

        return $this->successResponse($response);
    }

    public function cancelOrder(Request $request)
    {
        $transactionId = $request->input('transaction_id');
        $order = Order::where('id', $transactionId)->first();
        if (! $order) {
            return $this->failedResponse('Transaction not found');
        }

        $order->status = OrderStatusEnum::CANCELED;
        $order->save();

        $response = ['success' => true, 'message' => 'Transaction canceled successfully'];
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
