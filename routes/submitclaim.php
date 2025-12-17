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
            Route::get('/{id}/print', 'print')->name('v1.submit-claim.print');
            Route::get('/create', 'create')->name('v1.submit-claim.create');
            Route::get('/{id}/edit', 'edit')->name('v1.submit-claim.edit');
            Route::post('/store', 'store')->name('v1.submit-claim.store');
            Route::put('/{id}/update', 'update')->name('v1.submit-claim.update');
            Route::get('/list/data', 'getSubmitClaimsData')->name('v1.submit-claim.list.data');
            Route::get('/{submit_claim_id}/item-list/data', 'getSubmitClaimItemsData')->name('v1.submit-claim.item-list.data');
            Route::get('/submit-claim-items/{id}/details', 'getSubmitClaimItemDetails')->name('v1.submit-claim.submit-claim-items.details');
            Route::delete('/submit-claim-items/{id}/destroy', 'submitClaimDestroy')->name('v1.submit-claim.submit-claim-items.destroy');
            Route::delete('/{id}/destroy', 'destroyClaim')->name('v1.submit-claim.destroy');
            Route::post('/{id}/update-status','submitClaimUpdateStatus')->name('v1.submit-claim.update-status');
            Route::put('/{id}/update-description',  'updateDescription')->name('v1.submit-claim.update-description');
            Route::post('/{id}/action', 'handleApprovalAction')->name('v1.submit-claim.action');
            Route::post('/{id}/action-claim-item', 'handleRejectedAction')->name('v1.submit-claim.action-claim-item');
            Route::get('/exchange-rates', 'exchange')->name('v1.submit-claim.exchange-rates');
            Route::get('/exchange-rates/data', 'getRates')->name('v1.submit-claim.exchange-rates.data');
            Route::post('/adjust-claim-item', 'adjustClaimItem')->name('v1.submit-claim.adjust-claim-item');
            //->middleware('can:approve-claims');
        });
    });