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

use App\Http\Controllers\Website\HomeController;
use Illuminate\Support\Facades\Route;

Route::multilingual('/', [HomeController::class, 'index'])->name('website-home');
