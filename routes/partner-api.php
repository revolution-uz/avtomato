<?php

use App\Http\Controllers\PartnerApi\V1\AttachmentController;
use App\Http\Controllers\PartnerApi\V1\AuthController;
use App\Http\Controllers\PartnerApi\V1\InfoController;
use App\Http\Controllers\PartnerApi\V1\Installment\CalculatorController;
use App\Http\Controllers\PartnerApi\V1\Installment\TariffsController;
use App\Http\Controllers\PartnerApi\V1\Management\BranchController;
use App\Http\Controllers\PartnerApi\V1\User\ProfileController;
use App\Http\Controllers\PartnerApi\V1\User\UserController;
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
        Route::prefix('tariffs')->controller(TariffsController::class)->group(function () {
            Route::get('/', 'index')->name('tariffs.index');
            Route::get('/{tariff}', 'getSingle')->name('tariffs.get-single');
            Route::post('/', 'create')->name('tariffs.create');
            Route::put('/{tariff}', 'update')->name('tariffs.update');
            Route::delete('/', 'delete')->name('tariffs.delete');
        });

        // calculator
        Route::prefix('calculator')->controller(CalculatorController::class)->group(function () {
            Route::post('/calculate', 'calculate')->name('installment.calculator');
            Route::post('/export/{type}', 'export')->name('installment.export-calculator');
        });
    });

    // Management module (branches, users, roles, permissions and etc.)
    Route::prefix('management')->group(function () {
        // branches
        Route::prefix('branches')->controller(BranchController::class)->group(function () {
            Route::get('/', 'index')->name('branches.index');
            Route::get('/{branch}', 'getSingle')->name('branches.get-single');
            Route::post('/', 'create')->name('branches.create');
            Route::put('/{branch}', 'update')->name('branches.update');
            Route::delete('/', 'delete')->name('branches.delete');
        });
    });

    // profile
    Route::prefix('profile')->controller(ProfileController::class)->group(function () {
        Route::get('/me', 'me')->name('user.me');
        Route::put('/me', 'update')->name('user.update');
        Route::post('/change-password', 'changePassword')->name('user.change-password');
        Route::post('/change-theme', 'changeTheme')->name('user.change-password');
        Route::get('/devices', 'devices')->name('user.devices');
        Route::delete('/revoke-device/{personalAccessToken}', 'revokeDevice')->name('user.revoke-device');
    });

    Route::prefix('users')->controller(UserController::class)->group(function () {
        Route::get('/', 'index')->name('users.index');
        Route::get('/{user}', 'getSingle')->name('users.get-single');
        Route::post('/', 'create')->name('users.create');
        Route::put('/{user}', 'update')->name('users.update');
        Route::post('/{user}/change-password', 'changePassword')->name('users.get-single');
        Route::delete('/{user}', 'delete')->name('users.delete');
    });

    Route::prefix('info')->controller(InfoController::class)->group(function () {
        Route::get('/settings', 'settings')->name('info.settings');
    });

    Route::prefix('attachments')->controller(AttachmentController::class)->group(function () {
        Route::post('/upload', 'upload')->name('info.settings');
        Route::get('/{attachment}', 'getSingle')->name('info.settings');
    });


});
