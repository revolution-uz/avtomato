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

use App\Http\Controllers\Website\BlogsController;
use App\Http\Controllers\Website\HomeController;
use App\Http\Controllers\Website\PricesController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('blogs', [BlogsController::class, 'index'])->name('blogs');
Route::get('blogs/{slug}', [BlogsController::class, 'navigateToBlog']);
Route::get('pricing', [PricesController::class, 'index'])->name('prices');
Route::get('modules/installment', [PricesController::class, 'navigateToInstallment'])->name('installment');
Route::get('modules/marketing', [PricesController::class, 'navigateToMarketing'])->name('marketing');
Route::get('modules/scoring', [PricesController::class, 'navigateToScoring'])->name('scoring');
Route::get('modules/qr-pay', [PricesController::class, 'navigateToQR'])->name('sbp_pay');
Route::get('modules/ecommerce', [PricesController::class, 'navigateToEcommerce'])->name('ecommerce');
Route::get('modules/loyalty', [PricesController::class, 'navigateToLoyalty'])->name('loyalty_program');
Route::get('modules/others', [PricesController::class, 'navigateToOthers'])->name('others');
Route::post('new-customer', [HomeController::class, 'newCustomer'])->name('new_customer');
