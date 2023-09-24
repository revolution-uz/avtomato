<?php

use App\Http\Controllers\PartnerApi\V1\AuthController;
use App\Http\Controllers\PartnerApi\V1\InfoController;
use App\Http\Controllers\PartnerApi\V1\Installment\CalculatorController;
use App\Http\Controllers\PartnerApi\V1\Installment\TariffsController;
use App\Http\Controllers\PartnerApi\V1\Management\BranchController;
use App\Http\Controllers\PartnerApi\V1\User\ProfileController;
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
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum')->name('user.logout');

Route::group(['middleware' => ['auth:sanctum']], function () {
    // installments module
    Route::prefix('installment')->group(function () {
        // tariffs
        Route::prefix('tariffs')->group(function () {
            Route::get('/', [TariffsController::class, 'index'])->name('tariffs.index');
            Route::get('/{tariff}', [TariffsController::class, 'getSingle'])->name('tariffs.get-single');
            Route::post('/', [TariffsController::class, 'create'])->name('tariffs.create');
            Route::put('/{tariff}', [TariffsController::class, 'update'])->name('tariffs.update');
            Route::delete('/{tariff}', [TariffsController::class, 'delete'])->name('tariffs.delete');
        });

        // calculator
        Route::prefix('calculator')->group(function () {
            Route::post('/calculate', [CalculatorController::class, 'calculate'])->name('installment.calculator');
            Route::post('/export/{type}', [CalculatorController::class, 'export'])->name('installment.export-calculator');
        });
    });

    // Management module (branches, users, roles, permissions and etc.)
    Route::prefix('management')->group(function () {
        // tariffs
        Route::prefix('branches')->group(function () {
            Route::get('/', [BranchController::class, 'index']);
            Route::get('/{branch}', [BranchController::class, 'getSingle']);
            Route::post('/', [BranchController::class, 'create']);
            Route::put('/{branch}', [BranchController::class, 'update']);
            Route::delete('/{branch}', [BranchController::class, 'delete']);
        });
    });

    // profile
    Route::prefix('profile')->group(function () {
        Route::get('/me', [ProfileController::class, 'me'])->name('user.me');
        Route::get('/devices', [ProfileController::class, 'devices'])->name('user.devices');
        Route::delete('/revoke-device/{personalAccessToken}', [ProfileController::class, 'revokeDevice'])->name('user.revoke-device');
    });

    Route::prefix('info')->group(function () {
        Route::get('/settings', [InfoController::class, 'settings'])->name('info.settings');
    });
});
