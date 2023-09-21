<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\InstallmentController;
use App\Http\Controllers\Api\V1\ProfileController;
use App\Http\Controllers\Api\V1\TariffsController;
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

Route::post('/login', [AuthController::class, 'login']);

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('user.logout');
    Route::get('/profile/me', [ProfileController::class, 'me'])->name('user.me');
    Route::post('/profile/revoke-device/{id}', [ProfileController::class, 'revokeDevice'])->name('user.revoke-device');
    Route::get('/profile/devices', [ProfileController::class, 'devices'])->name('user.devices');
    Route::get('/tariffs', [TariffsController::class, 'index'])->name('tariffs.index');
    Route::get('/tariffs/{id}', [TariffsController::class, 'getSingle'])->name('tariffs.get-single');
    Route::post('/tariffs/create', [TariffsController::class, 'create'])->name('tariffs.create');
    Route::post('/tariffs/{id}/update', [TariffsController::class, 'update'])->name('tariffs.update');
    Route::post('/installment/calculator', [InstallmentController::class, 'calculator'])->name('installment.calculator');

});
