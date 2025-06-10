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
use App\Http\Controllers\V2\ClientController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v2')->group(function () {
    // 🔹 Client Database Management Routes
        Route::prefix('client-database')->controller(ClientController::class)->group(function () {
            Route::get('/', 'index')->name('v2.client-database');
            Route::get('/list', 'list')->name('v2.client-database.list');
            Route::get('/data', 'getClientsData')->name('v2.client-database.data');
            Route::put('/assignment-salesperson', 'assignmentSalesperson')->name('v2.client-database.assignment-salesperson');            
            Route::put('/bulk-assignment-salesperson', 'bulkAssignmentSalesperson')->name('v2.client-database.bulk-assignment-salesperson');
            Route::delete('/bulk-delete', 'bulkDelete')->name('v2.client-database.bulk-delete');
            Route::put('/bulk-request-to-edit', 'bulkRequestToEdit')->name('v2.client-database.bulk-request-to-edit');
        });
});

