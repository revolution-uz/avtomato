<?php

use App\Http\Controllers\PartnerApi\DocumentController;
use App\Http\Controllers\PartnerApi\LoyaltyProgramOrderController;
use App\Http\Controllers\PartnerApi\LoyaltyProgramSyncController;
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
Route::group(['middleware' => ['auth.loyalty_program_sync']], function () {
    // Loyalty Program Sync
    Route::prefix('sync')->controller(LoyaltyProgramSyncController::class)->group(function () {
        Route::post('shops', 'postShops')->name('sync.shops');
        Route::post('items', 'postItems')->name('sync.items');
        Route::post('prices', 'postPrices')->name('sync.prices');
        Route::post('stocks', 'postStocks')->name('sync.stocks');
    });
    Route::prefix('order')->controller(LoyaltyProgramOrderController::class)->group(function () {
        Route::post('create', 'createOrder')->name('order.create');
        Route::post('complete', 'completeOrder')->name('order.complete');
        Route::post('cancel', 'cancelOrder')->name('order.cancel');
    });
});
