<?php

namespace App\Http\Controllers\PartnerApi;

use App\DTOs\LoyaltyProgram\CartDTO;
use App\Http\Controllers\Controller;
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

    public function confirmOrder(Request $request)
    {
        $transactionId = $request->input('transaction_id');
        $transaction = Order::where('id', $transactionId)->first();
        if (!$transaction) {
            return $this->failedResponse('Transaction not found');
        }

        $transaction->status = 'confirmed';
        $transaction->save();

        $response = ['success' => true, 'message' => 'Transaction confirmed successfully'];
        $this->logging($request, $response, $this->channel, $this->type, 'confirmOrder');

        return $this->successResponse($response);
    }

    public function cancelOrder(Request $request)
    {
        $transactionId = $request->input('transaction_id');
        $transaction = Order::where('id', $transactionId)->first();
        if (!$transaction) {
            return $this->failedResponse('Transaction not found');
        }

        $transaction->status = 'canceled';
        $transaction->save();

        $response = ['success' => true, 'message' => 'Transaction canceled successfully'];
        $this->logging($request, $response, $this->channel, $this->type, 'cancelOrder');

        return $this->successResponse($response);
    }
}
