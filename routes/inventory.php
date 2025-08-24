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
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReceivingOrderController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // 🔹 Client Database Management Routes
        Route::prefix('inventory-management')->controller(ProductController::class)->group(function () {
            Route::get('/', 'index')->name('v1.inventory-management');
            Route::get('/list', 'list')->name('v1.inventory-management.list');
            Route::get('/all', 'all')->name('v1.submit-claim.all');
            Route::get('/list/data', 'getProductsData')->name('v1.inventory-management.list.data');
            Route::get('/{product}/detail', 'show')->name('v1.inventory-management.detail');
            Route::get('/create', 'create')->name('v1.inventory-management.create');
            Route::get('/{id}/edit', 'edit')->name('v1.inventory-management.edit');
            Route::post('/store', 'store')->name('v1.inventory-management.store');
            Route::post('/stock-adjustment', 'adjustStock')->name('v1.inventory-management.stock-adjustment');
            Route::put('/{id}/update', 'update')->name('v1.inventory-management.update');
            Route::post('/product-category/store','categoryStore');
            Route::put('/{id}/product-category/update','categoryUpdate');
            Route::delete('/{id}/product-category/delete','categoryDelete');
            Route::get('/{productId}/history','getProductHistory')->name('v1.inventory-management.history');
            //->middleware('can:approve-claims');
        });
    });

Route::prefix('v1')->group(function () {
    // 🔹 Client Database Management Routes
        Route::prefix('receiving-order')->controller(ReceivingOrderController::class)->group(function () {
            Route::get('/list', 'list')->name('v1.receiving-order.list');
            Route::get('/{id}/show', 'show')->name('v1.receiving-order.show');
            Route::get('/data', 'getReceivingOrderData')->name('v1.receiving-order.data');
            Route::get('/create', 'create')->name('v1.receiving-order.create');
            Route::post('/store', 'store')->name('v1.receiving-order.store');
            Route::get('/edit', 'list')->name('v1.receiving-order.edit');
            Route::put('/update', 'list')->name('v1.receiving-order.update');
            Route::put('/delete', 'list')->name('v1.receiving-order.delete');
        });
    });