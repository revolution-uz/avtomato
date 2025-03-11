<?php

/*
|--------------------------------------------------------------------------
| Website Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Http\Controllers\PartnerApi\QrPayController;
use App\Http\Controllers\PartnerPanelController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PartnerPanelController::class, 'index']);

Route::group(['middleware' => ['optional_auth:sanctum']], function () {
    // qr pay
    Route::prefix('qr-pay')->controller(QrPayController::class)->group(function () {
        Route::view('/', 'partner-api.qr-pay.form');
        Route::post('/', 'create')->name('qr-pay.create');
        Route::get('state', 'check')->name('qr-pay.check');
    });
});
