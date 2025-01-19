<?php

namespace App\Http\Controllers\PartnerApi;

use App\Http\Controllers\Controller;
use App\Services\CompanyQrPayService;
use Exception;
use Illuminate\Http\Request;

class QrPayController extends Controller
{
    public function __construct(private readonly CompanyQrPayService $qrPayService) {}

    public function create(Request $request)
    {
        $request->validate(['amount' => 'required|numeric|min:1']);

        try {
            return redirect($this->qrPayService->createPayment($request->input('amount'))->payment->qr->url);
        } catch (Exception $e) {
            throw new Exception('Failed to create cheque: ' . $e->getMessage());
        }
    }

    public function check(Request $request)
    {
        $request->validate(['ext_id' => 'required']);

        return $this->qrPayService->statePayment($request->input('ext_id'));
    }
}
