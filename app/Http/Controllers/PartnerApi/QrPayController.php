<?php

namespace App\Http\Controllers\PartnerApi;

use App\Http\Controllers\Controller;
use App\Http\Requests\QrPayCreatePaymentRequest;
use App\Services\QrPayService;
use Exception;

class QrPayController extends Controller
{
    public function __construct(
        private readonly QrPayService $qrPayService
    ) {}

    public function createPayment(QrPayCreatePaymentRequest $request)
    {
        try {
            if ($result = $this->qrPayService->createPayment($request)) {
                redirect($result->payment->qr->url);
            }
        } catch (Exception $e) {
            throw new Exception('Failed to create cheque: ' . $e->getMessage());
        }
    }

    public function checkPayment(string $ext_id)
    {
        return $this->qrPayService->statePayment($ext_id);
    }
}
