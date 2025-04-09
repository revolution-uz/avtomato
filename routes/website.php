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

use App\Http\Controllers\Website\BlogController;
use App\Http\Controllers\Website\HomeController;
use App\Http\Controllers\Website\ModulesController;
use App\Http\Controllers\Website\PricingController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('blog/{slug}', [BlogController::class, 'show'])->name('blog.show');
Route::get('blog/category/{category}', [BlogController::class, 'byCategory'])->name('blog.by_category');

Route::get('pricing', [PricingController::class, 'index'])->name('pricing');

Route::get('modules/installment', [ModulesController::class, 'installment'])->name('modules/installment');
Route::get('modules/marketing', [ModulesController::class, 'marketing'])->name('modules/marketing');
Route::get('modules/scoring', [ModulesController::class, 'scoring'])->name('modules/scoring');
Route::get('modules/qr-pay', [ModulesController::class, 'sbpPay'])->name('modules/sbp_pay');
Route::get('modules/ecommerce', [ModulesController::class, 'ecommerce'])->name('modules/ecommerce');
Route::get('modules/loyalty', [ModulesController::class, 'loyalty'])->name('modules/loyalty_program');
Route::get('modules/others', [ModulesController::class, 'others'])->name('modules/others');

Route::post('new-customer', [HomeController::class, 'newCustomer'])->name('new_customer');
