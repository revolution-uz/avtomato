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
use App\Http\Controllers\Website\ModulesController;
use App\Http\Controllers\Website\PricingController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('blog', [BlogsController::class, 'index'])->name('blog.index');
Route::get('blog/{slug}', [BlogsController::class, 'show'])->name('blog.show');

Route::get('pricing', [PricingController::class, 'index'])->name('pricing');


Route::get('modules/installment', [ModulesController::class, 'navigateToInstallment'])->name('modules/installment');
Route::get('modules/marketing', [ModulesController::class, 'navigateToMarketing'])->name('modules/marketing');
Route::get('modules/scoring', [ModulesController::class, 'navigateToScoring'])->name('modules/scoring');
Route::get('modules/qr-pay', [ModulesController::class, 'navigateToQR'])->name('modules/sbp_pay');
Route::get('modules/ecommerce', [ModulesController::class, 'navigateToEcommerce'])->name('modules/ecommerce');
Route::get('modules/loyalty', [ModulesController::class, 'navigateToLoyalty'])->name('modules/loyalty_program');
Route::get('modules/others', [ModulesController::class, 'navigateToOthers'])->name('modules/others');
