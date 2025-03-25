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
Route::get('/blogs', [BlogsController::class, 'index'])->name('blogs');
Route::get('/blogs/{slug}', [BlogsController::class, 'navigateToBlog']);
Route::get('/prices', [PricesController::class, 'index'])->name('prices');
Route::get('/prices/installment', [PricesController::class, 'navigateToInstallment'])->name('installment');
Route::get('/prices/marketing', [PricesController::class, 'navigateToMarketing'])->name('marketing');
Route::get('/prices/scoring', [PricesController::class, 'navigateToScoring'])->name('scoring');
Route::get('/prices/qr-pay', [PricesController::class, 'navigateToQR'])->name('sbp_pay');
Route::get('/prices/ecommerce', [PricesController::class, ''])->name('ecommerce');
Route::get('/prices/loyalty', [PricesController::class, ''])->name('loyalty_program');
Route::get('/prices/others', [PricesController::class, ''])->name('others');