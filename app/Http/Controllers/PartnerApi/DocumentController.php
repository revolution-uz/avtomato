<?php

namespace App\Http\Controllers\PartnerApi;

use App\Http\Controllers\Controller;
use App\Http\Requests\DownloadCustomerApplicationRequest;
use App\Http\Requests\DownloadGuarantorApplicationRequest;
use App\Http\Requests\DownloadInstallmentAgreementRequest;
use App\Models\Company\Customer;
use App\Models\Company\Installment;
use App\Services\DocumentGeneratorService;

class DocumentController extends Controller
{
    public function __construct(
        private readonly DocumentGeneratorService $documentGeneratorService
    ) {}

    public function downloadCustomerApplication(DownloadCustomerApplicationRequest $request)
    {
        return $this->documentGeneratorService->generateCustomerApplicationStream(Installment::find($request->installment_id));
    }

    public function downloadInstallmentAgreement(DownloadInstallmentAgreementRequest $request)
    {
        return $this->documentGeneratorService->generateInstallmentFormStream(Installment::find($request->installment_id));
    }

    public function downloadGuarantorApplication(DownloadGuarantorApplicationRequest $request)
    {
        return $this->documentGeneratorService->generateGuarantorApplicationStream(
            Installment::find($request->installment_id),
            Customer::find($request->guarantor_id)
        );
    }
}
