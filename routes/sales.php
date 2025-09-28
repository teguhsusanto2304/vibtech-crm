<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\NewPasswordController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
use App\Http\Controllers\SalesForecastController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // 🔹 Client Database Management Routes
        Route::prefix('sales-forecast')->controller(SalesForecastController::class)->group(function () {
            Route::get('/', 'index')->name('v1.sales-forecast');
            Route::get('/create', 'create')->name('v1.sales-forecast.create');
            Route::post('/store', 'store')->name('v1.sales-forecast.store');
            
        });
    });