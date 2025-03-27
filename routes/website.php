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
Route::get('blog', [BlogsController::class, 'index'])->name('blog.index');
Route::get('blog/{slug}', [BlogsController::class, 'navigateToBlog'])->name('blog.show');
Route::get('pricing', [PricesController::class, 'index'])->name('pricing');
Route::get('modules/installment', [PricesController::class, 'navigateToInstallment'])->name('modules/installment');
Route::get('modules/marketing', [PricesController::class, 'navigateToMarketing'])->name('modules/marketing');
Route::get('modules/scoring', [PricesController::class, 'navigateToScoring'])->name('modules/scoring');
Route::get('modules/qr-pay', [PricesController::class, 'navigateToQR'])->name('modules/sbp_pay');
Route::get('modules/ecommerce', [PricesController::class, 'navigateToEcommerce'])->name('modules/ecommerce');
Route::get('modules/loyalty', [PricesController::class, 'navigateToLoyalty'])->name('modules/loyalty_program');
Route::get('modules/others', [PricesController::class, 'navigateToOthers'])->name('modules/others');
