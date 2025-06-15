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
use App\Http\Controllers\ProjectStageTaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // 🔹 Client Database Management Routes
        Route::prefix('project-management')->controller(ProjectController::class)->group(function () {
            Route::get('/', 'index')->name('v1.project-management');
            Route::get('/list', 'list')->name('v1.project-management.list');
            Route::get('/create', 'create')->name('v1.project-management.create');
            Route::put('/{id}/update', 'update')->name('v1.project-management.update');            
            Route::post('/{id}/add-member', 'addMember')->name('v1.project-management.add-member');
            Route::delete('/{project_id}/remove-member/{user_id}', 'removeMember')->name('v1.project-management.remove-member');
            Route::get('/{project}/detail', 'detail')->name('v1.project-management.detail');
            Route::put('/{project_id}/complete', 'markProjectComplete')->name('v1.project-management.complete');
            Route::put('/{project_id}/stages/{project_stage_id}/complete', 'markComplete')->name('v1.project-management.stages.complete');
            Route::get('/{id}/edit', 'edit')->name('v1.project-management.edit');
            Route::delete('/{project}/destroy', 'detail')->name('v1.project-management.destroy');
            Route::post('/store', 'store')->name('v1.project-management.store');
            Route::get('/data', 'getProjectsData')->name('v1.project-management.data');
        });

        Route::prefix('project-management')->controller(ProjectStageTaskController::class)->group(function () {
            Route::post('/{project_id}/stages/{stage_id}/tasks', 'store')->name('v1.project-management.stage.tasks');
        });
});

