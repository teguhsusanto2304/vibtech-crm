<?php

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
use App\Http\Controllers\LeaveApplicationController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // 🔹 Client Database Management Routes
        Route::prefix('leave-application')->controller(LeaveApplicationController::class)->group(function () {
            Route::get('/', 'index')->name('v1.leave-application');
            Route::get('/list', 'list')->name('v1.leave-application.list');
            Route::get('/data','getLeaveApplicationData')->name('v1.leave-application.data');
            Route::get('/create','create')->name('v1.leave-application.create');
            Route::get('/{id}/edit','edit')->name('v1.leave-application.edit');
            Route::post('/store','store')->name('v1.leave-application.store');
            Route::put('/{id}/update','update')->name('v1.leave-application.update');
            Route::delete('/{id}/destroy','destroy')->name('v1.leave-application.destroy');
            Route::post('/import','import')->name('v1.leave-application.import');
            Route::get('/template-download','downloadTemplate')->name('v1.leave-application.template-download');

            Route::get('/public-holiday/{id}/modal', 'publicHolidayShow')->name('v1.leave-application.public-holiday.modal');
            //->middleware('can:approve-claims');
        });
    });