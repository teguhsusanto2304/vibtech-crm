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
use App\Http\Controllers\MeetingMinuteController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // 🔹 Client Database Management Routes
        Route::prefix('meeting-minutes')->controller(MeetingMinuteController::class)->group(function () {
            Route::get('/', 'index')->name('v1.meeting-minutes');
            Route::get('/list', 'list')->name('v1.meeting-minutes.list');
            Route::get('/all', 'all')->name('v1.meeting-minutes.all');
            Route::get('/{id}/detail', 'show')->name('v1.meeting-minutes.detail');
            Route::get('/create', 'create')->name('v1.meeting-minutes.create');
            Route::post('/store', 'store')->name('v1.meeting-minutes.store');
            Route::get('/list/data', 'getMeetingMinutesData')->name('v1.meeting-minutes.list.data');
            Route::get('/{submit_claim_id}/item-list/data', 'getSubmitClaimItemsData')->name('v1.meeting-minutes.item-list.data');
            Route::get('/meeting-minutes-items/{id}/details', 'getSubmitClaimItemDetails')->name('v1.meeting-minutes.meeting-minutes-items.details');
            Route::delete('/meeting-minutes-items/{id}/destroy', 'submitClaimDestroy')->name('v1.meeting-minutes.meeting-minutes-items.destroy');
            Route::delete('/{id}/destroy', 'destroyClaim')->name('v1.meeting-minutes.destroy');
            Route::post('/{id}/update-status','submitClaimUpdateStatus')->name('v1.meeting-minutes.update-status');
            Route::post('/{id}/action', 'handleApprovalAction')->name('v1.meeting-minutes.action');
            //Route::get('/bulk-export-pdf','pdf')->name('v1.meeting-minutes.bulk-export-pdf');
            Route::get('/bulk-export-pdf', 'bulkExportPdf')->name('v1.meeting-minutes.bulk-export-pdf');
            Route::get('/{id}/download-pdf','downloadPdf')->name('v1.meeting-minutes.download-pdf');
            //->middleware('can:approve-claims');
        });
    });