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
use App\Http\Controllers\ProjectController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // 🔹 Client Database Management Routes
        Route::prefix('project-management')->controller(ProjectController::class)->group(function () {
            Route::get('/', 'index')->name('v1.project-management');
            Route::get('/list', 'list')->name('v1.project-management.list');
            Route::get('/create', 'create')->name('v1.project-management.create');
            Route::get('/{project}/detail', 'detail')->name('v1.project-management.detail');
            Route::post('/store', 'store')->name('v1.project-management.store');
            Route::get('/data', 'getProjectsData')->name('v1.project-management.data');
        });
});

