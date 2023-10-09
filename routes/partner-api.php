<?php

use App\Http\Controllers\PartnerApi\V1\AuthController;
use App\Http\Controllers\PartnerApi\V1\Customer\CustomerController;
use App\Http\Controllers\PartnerApi\V1\Info\InfoController;
use App\Http\Controllers\PartnerApi\V1\Installment\CalculatorController;
use App\Http\Controllers\PartnerApi\V1\Installment\InstallmentController;
use App\Http\Controllers\PartnerApi\V1\Installment\TariffsController;
use App\Http\Controllers\PartnerApi\V1\Installment\TransactionController;
use App\Http\Controllers\PartnerApi\V1\Installment\TransactionTypeController;
use App\Http\Controllers\PartnerApi\V1\Management\BranchController;
use App\Http\Controllers\PartnerApi\V1\Scoring\KatmCreditHistoryController;
use App\Http\Controllers\PartnerApi\V1\Scoring\KatmInpsHistoryController;
use App\Http\Controllers\PartnerApi\V1\Scoring\KatmPensionInfoController;
use App\Http\Controllers\PartnerApi\V1\User\ProfileController;
use App\Http\Controllers\PartnerApi\V1\User\RoleController;
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
Route::post('login', [AuthController::class, 'login'])->name('auth.login');
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum')->name('auth.logout');

Route::group(['middleware' => ['auth:sanctum']], function () {
    // installments module
    Route::prefix('installment')->group(function () {
        // tariffs
        Route::prefix('tariffs')->controller(TariffsController::class)->group(function () {
            Route::get('/', 'index')->name('tariffs.index');
            Route::get('{id}', 'getSingle')->name('tariffs.get-single');
            Route::post('/', 'create')->name('tariffs.create');
            Route::put('{id}', 'update')->name('tariffs.update');
            Route::delete('/', 'delete')->name('tariffs.delete');
        });

        // calculator
        Route::prefix('calculator')->controller(CalculatorController::class)->group(function () {
            Route::post('calculate', 'calculate')->name('installment.calculator');
            Route::post('export/{type}', 'export')->name('installment.export-calculator');
        });

        // installments
        Route::prefix('installment')->controller(InstallmentController::class)->group(function () {
            Route::get('/', 'index')->name('installments.index');
            Route::get('{installment}', 'getSingle')->name('installments.get-single');
            Route::post('/', 'create')->name('installments.create');
            Route::put('{installment}', 'update')->name('installments.update');
            Route::delete('/', 'delete')->name('installments.delete');
            Route::post('card', 'attachCard')->name('installments.attach-card');
            Route::delete('card', 'detachCard')->name('installments.detach-card');
            Route::post('guarantor', 'attachGuarantor')->name('installments.attach-guarantor');
            Route::put('guarantor/{installmentGuarantor}', 'updateGuarantor')->name('installments.update-guarantor');
            Route::delete('guarantor/{installmentGuarantor}', 'detachGuarantor')->name('installments.detach-guarantor');
        });

        // transactions
        Route::prefix('transaction')->controller(TransactionController::class)->group(function () {
            Route::get('/', 'index')->name('transactions.index');
            Route::get('{transaction}', 'getSingle')->name('transactions.get-single');
            Route::post('/', 'create')->name('transactions.create');
            Route::put('{transaction}', 'update')->name('transactions.update');
            Route::delete('/', 'delete')->name('transactions.delete');
        });

        // transaction_types
        Route::prefix('transaction-type')->controller(TransactionTypeController::class)->group(function () {
            Route::get('/', 'index')->name('transaction-types.index');
            Route::get('{transactionType}', 'getSingle')->name('transaction-types.get-single');
            Route::post('/', 'create')->name('transaction-types.create');
            Route::put('{transactionType}', 'update')->name('transaction-types.update');
            Route::delete('/', 'delete')->name('transaction-types.delete');
        });

    });

    // Management module (branches, users, roles, permissions and etc.)
    Route::prefix('management')->group(function () {
        // branches
        Route::prefix('branches')->controller(BranchController::class)->group(function () {
            Route::get('/', 'index')->name('branches.index');
            Route::get('{id}', 'getSingle')->name('branches.get-single');
            Route::post('/', 'create')->name('branches.create');
            Route::put('{id}', 'update')->name('branches.update');
            Route::delete('/', 'delete')->name('branches.delete');
        });
    });

    // profile
    Route::prefix('profile')->controller(ProfileController::class)->group(function () {
        Route::get('me', 'me')->name('profile.me');
        Route::put('me', 'update')->name('profile.update');
        Route::post('change-password', 'changePassword')->name('profile.change-password');
        Route::post('change-theme', 'changeTheme')->name('profile.change-theme');
        Route::post('change-lang', 'changeLanguage')->name('profile.change-language');
        Route::post('change-avatar', 'uploadAvatar')->name('profile.change-avatar');
        Route::get('devices', 'devices')->name('profile.devices');
        Route::delete('revoke-device/{id}', 'revokeDevice')->name('profile.revoke-device');
    });

    Route::prefix('users')->controller(UserController::class)->group(function () {
        Route::get('/', 'index')->name('users.index');
        Route::get('{id}', 'getSingle')->name('users.get-single');
        Route::post('/', 'create')->name('users.create');
        Route::put('{id}', 'update')->name('users.update');
        Route::post('{id}/change-password', 'changePassword')->name('users.change-password');
        Route::post('{id}/change-theme', 'changeTheme')->name('users.change-theme');
        Route::post('{id}/change-avatar', 'uploadAvatar')->name('users.change-avatar');
        Route::get('{id}/devices', 'devices')->name('users.devices');
        Route::post('{id}/attach-role', 'attachRole')->name('users.attach-role');
        Route::delete('revoke-device', 'revokeDevice')->name('users.revoke-device');
        Route::delete('/', 'delete')->name('users.delete');
    });

    Route::prefix('roles')->controller(RoleController::class)->group(function () {
        Route::get('/', 'index')->name('roles.index');
        Route::get('{id}', 'getSingle')->name('roles.get-single');
        Route::post('/', 'create')->name('roles.create');
        Route::put('{id}', 'update')->name('roles.update');
        Route::post('{id}/attach-permissions', 'attachPermissions')->name('roles.attach-permissions');
        Route::delete('/', 'delete')->name('roles.delete');
    });

    Route::prefix('info')->controller(InfoController::class)->group(function () {
        Route::get('settings', 'settings')->name('info.settings');
        Route::get('regions', 'regions')->name('info.regions');
        Route::get('districts', 'districts')->name('info.districts');
        Route::get('mahallas', 'mahallas')->name('info.mahallas');
    });

    Route::prefix('customer')->controller(CustomerController::class)->group(function () {
        Route::post('info-by-passport', 'infoByPassport')->name('customer.info-by-passport');
        Route::post('create', 'create')->name('customer.create');
        Route::get('/', 'index')->name('customer.index');
        Route::get('{id}', 'getSingle')->name('customer.get-single');
    });

    Route::prefix('scoring')->group(function () {
        Route::prefix('credit-history')->controller(KatmCreditHistoryController::class)->group(function () {
            Route::post('create', 'create');
            Route::get('/', 'index');
            Route::get('{id}', 'read');
        });
        Route::prefix('inps-history')->controller(KatmInpsHistoryController::class)->group(function () {
            Route::post('create', 'create');
            Route::get('/', 'index');
            Route::get('{id}', 'read');
        });
        Route::prefix('pension-info')->controller(KatmPensionInfoController::class)->group(function () {
            Route::post('create', 'create');
            Route::get('/', 'index');
        });
    });
});
