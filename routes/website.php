<?php

use App\Http\Controllers\template\laravel_example\UserManagement;
use Illuminate\Support\Facades\Route;


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

Route::get('/', 'App\Http\Controllers\Website\HomeController@index')->name('website-home');
