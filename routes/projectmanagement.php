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
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // 🔹 Client Database Management Routes
        Route::prefix('project-management')->controller(ProjectController::class)->group(function () {
            Route::get('/', 'index')->name('v1.project-management');
            Route::get('/list', 'list')->name('v1.project-management.list');
            Route::get('/all', 'all')->name('v1.project-management.all');
            Route::get('/create', 'create')->name('v1.project-management.create');
            Route::put('/{id}/update', 'update')->name('v1.project-management.update');            
            Route::post('/{id}/add-member', 'addMember')->name('v1.project-management.add-member');
            Route::delete('/{project_id}/remove-member/{user_id}', 'removeMember')->name('v1.project-management.remove-member');
            Route::get('/{project}/detail', 'detail')->name('v1.project-management.detail');
            Route::get('/{project_id}/{id}/phase', 'phase')->name('v1.project-management.phase');
            Route::get('/{project}/management-detail', 'managementDetail')->name('v1.project-management.management-detail');
            Route::put('/{project_id}/complete', 'markProjectComplete')->name('v1.project-management.complete');
            Route::put('/{project_id}/stages/{project_stage_id}/complete', 'markComplete')->name('v1.project-management.stages.complete');
            Route::put('/{project_id}/phases/{phase_id}/complete', 'completePhase')->name('v1.project-management.phases.complete');
            Route::get('/{id}/edit', 'edit')->name('v1.project-management.edit');
            Route::delete('/{project}/destroy', 'destroy')->name('v1.project-management.destroy');
            Route::delete('/{projectFileId}/file-destroy', 'fileDestroy')->name('v1.project-management.file-destroy');
            Route::post('/store', 'store')->name('v1.project-management.store');
            Route::get('/data', 'getProjectsData')->name('v1.project-management.data');
            Route::get('/all-data', 'getAllProjectsData')->name('v1.project-management.all-data');
            Route::get('/{id}/assignable-users','getAssignableUsers')->name('v1.project-management.assignable-users');
            Route::get('/project-stages/{projectStageId}/{projectId}/bulletins/data', 'getStageBulletinsData')->name('v1.project-management.project-stages.bulletins.data');
            Route::post('/project-stages/{projectStageId}/{projectId}/bulletins/store', 'storeBulletin')->name('v1.project-management.project-stages.bulletins.store');
            Route::get('/monthly-chart','monthlyStatusChartData')->name('v1.project-management.monthly-chart');
            Route::get('/project-files/data','getProjectFileData')->name('v1.project-management.project-files.data');
            Route::get('/{projectId}/phases/{phaseId}/get-details','getPhaseDetailsForModal')->name('v1.project-management.phases.get-details');
            Route::get('/{projectId}/stage/{stageId}/get-details','getStageDetailsForModal')->name('v1.project-management.stage.get-details');
            Route::put('/{projectId}/phases/{phaseId}','updateProjectPhase')->name('v1.project-management.phases');
            Route::get('/{projectId}/kanban','showKanban')->name('v1.project-management.kanban');
            Route::get('/{projectId}/gantt','showGantt')->name('v1.project-management.gantt');
            Route::get('/{projectId}/gantt-daily','showGanttDaily')->name('v1.project-management.gantt-daily');
        });

        Route::prefix('project-management')->controller(ProjectStageTaskController::class)->group(function () {
            Route::post('/{project_id}/stages/{stage_id}/tasks', 'store')->name('v1.project-management.stage.tasks');
            Route::post('/stages/{task_id}/tasks-update-status', 'updateStatus')->name('v1.project-management.stage.tasks-update-status');
            Route::post('/stages/{task_id}/tasks-update', 'update')->name('v1.project-management.stage.tasks-update');
            Route::delete('/stages/{task_id}/tasks-delete', 'destroy')->name('v1.project-management.stage.tasks-delete');
            Route::delete('/stages/{task_id}/tasks-destroy', 'destroy')->name('v1.project-management.stage.tasks-destroy');
            Route::post('/stages/{task_id}/tasks-add-log', 'addLog')->name('v1.project-management.stage.tasks-add-log');
            Route::get('/stage/{task_id}/tasks', 'show')->name('v1.project-management.stage.tasks');
            
        });

        Route::prefix('projects')->controller(ProjectController::class)->group(function () {
            Route::get('/list', 'projectList')->name('v1.projects.list');
            Route::get('/all', 'all')->name('v1.projects.all');
            Route::get('/create', 'createProject')->name('v1.projects.create');
            Route::post('/store','storeProject')->name('v1.projects.store');
            Route::post('/tasks/store','storeProjectTask')->name('v1.projects.tasks.store');
            Route::get('/{project}/detail', 'detailProject')->name('v1.projects.detail');
            Route::get('/{projectId}/gantt-daily','showGanttDaily')->name('v1.projects.gantt-daily');
            Route::get('/{project_id}/{id}/phase', 'phaseProject')->name('v1.projects.phase');
            Route::get('/{project_id}/{id}/stage', 'stageProject')->name('v1.projects.stage');
            Route::put('/{project_id}/{id}/phase/default', 'setDefaultPhase')->name('v1.projects.phase.default');
            Route::get('/{projectId}/board','showKanban')->name('v1.projects.board');
            Route::post('/{taskId}/tasks/move','moveProjectTask')->name('v1.projects.tasks.move');
            Route::get('/data', 'getProjectsData')->name('v1.projects.data');
            Route::post('/stages/store','storeProjectSTage')->name('v1.projects.stage.store');
        });
        
    });

        

        

