<?php

namespace App\Http\Controllers\PartnerApi;

use App\Http\Controllers\Controller;
use App\Http\Requests\QrPay\QrPayCreatePaymentRequest;
use App\Services\CompanyQrPayService;
use Exception;

class QrPayController extends Controller
{
    public function __construct(
        private readonly CompanyQrPayService $qrPayService
    ) {}

    public function create(QrPayCreatePaymentRequest $request)
    {
        try {
            redirect($this->qrPayService->createPayment($request)->payment->qr->url);
        } catch (Exception $e) {
            throw new Exception('Failed to create cheque: ' . $e->getMessage());
        }
    }

    public function check(string $ext_id)
    {
        return $this->qrPayService->statePayment($ext_id);
    }
}
