<?php

use App\Http\Controllers\PartnerApi\DocumentController;
use App\Http\Controllers\PartnerApi\QrPayController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group(['middleware' => ['auth:sanctum']], function () {
    // document
    Route::prefix('document')->controller(DocumentController::class)->group(function () {
        // Route::get('installment-agreement', 'downloadInstallmentAgreement')->name('document.downloadInstallmentAgreement');
        Route::get('customer-application', 'downloadCustomerApplication')->name('document.downloadCustomerApplication');
        Route::get('guarantor-application', 'downloadGuarantorApplication')->name('document.downloadGuarantorApplication');
    });
});
Route::group(['middleware' => ['optional_auth:sanctum']], function () {
    // qr pay
    Route::prefix('qr-pay')->controller(QrPayController::class)->group(function () {
        // Route::get('installment-agreement', 'downloadInstallmentAgreement')->name('document.downloadInstallmentAgreement');
        Route::get('', 'createPayment')->name('qr_pay.createPayment');
        Route::get('state', 'checkPayment')->name('qr_pay.checkPayment');
    });
});
