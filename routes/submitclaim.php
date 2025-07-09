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
use App\Http\Controllers\SubmitClaimController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // 🔹 Client Database Management Routes
        Route::prefix('submit-claim')->controller(SubmitClaimController::class)->group(function () {
            Route::get('/', 'index')->name('v1.submit-claim');
            Route::get('/list', 'list')->name('v1.submit-claim.list');
            Route::get('/all', 'all')->name('v1.submit-claim.all');
            Route::get('/{id}/detail', 'detail')->name('v1.submit-claim.detail');
            Route::get('/create', 'create')->name('v1.submit-claim.create');
            Route::post('/store', 'store')->name('v1.submit-claim.store');
            Route::get('/list/data', 'getSubmitClaimsData')->name('v1.submit-claim.list.data');
        });
    });