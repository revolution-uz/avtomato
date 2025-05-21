<?php

use App\Http\Controllers\QrPayCallbackController;
use App\Http\Controllers\SayqalController;
use App\Http\Controllers\UniredController;
use Illuminate\Http\Request;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::middleware(['log.sayqal', 'auth.sayqal'])->prefix('sayqal-callback')->controller(SayqalController::class)->group(function () {
    Route::post('sms', 'sms')->name('sayqalCallback.sms');
    Route::post('voice', 'voice')->name('sayqalCallback.voice');
});
Route::middleware('auth.unired')->prefix('unired-callback')->controller(UniredController::class)->group(function () {
    Route::post('web-hook', 'webHook')->name('uniredCallback.webHook');
    Route::post('check-payment', 'checkPayment')->name('uniredCallback.checkPayment');
    Route::post('send-sms', 'sendSms')->name('uniredCallback.sendSms');
});
Route::middleware('auth.qr-pay')->prefix('qr-pay')->controller(QrPayCallbackController::class)->group(function () {
    Route::post('web-hook', 'webHook')->name('qrPayCallback.webHook');
});
