<?php

use App\Http\Controllers\PartnerApi\DocumentController;
use App\Http\Controllers\PartnerApi\LoyaltyProgramSyncController;
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
        Route::view('/', 'partner-api.qr-pay.form');
        Route::post('/', 'create')->name('qr-pay.create');
        Route::get('state', 'check')->name('qr-pay.check');
    });
});
Route::group(['middleware' => ['auth.loyalty_program_sync']], function () {
    // Loyalty Program Sync
    Route::prefix('sync')->controller(LoyaltyProgramSyncController::class)->group(function () {
        Route::post('shops', 'postShops')->name('sync.shops');
        Route::post('items', 'postItems')->name('sync.items');
        Route::post('prices', 'postPrices')->name('sync.prices');
        Route::post('stocks', 'postStocks')->name('sync.stocks');
    });
});
