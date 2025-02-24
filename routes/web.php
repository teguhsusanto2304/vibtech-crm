<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

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
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\JobAssignmentController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;


Route::get('/', function () {
    return view('login');
});

Route::get('/login', function () {
    return view('login');
})->name('login');

Route::get('/v1/login', [LoginController::class, 'showLoginForm'])->name('v1.login');
Route::post('/v1/login', [LoginController::class, 'login'])->name('v1.login');

Route::middleware('auth')->group(function () {
    Route::get('/v1/dashboard', [DashboardController::class, 'index']
    )->name('v1.dashboard');
    Route::get('/v1/dashboard/events', [DashboardController::class, 'getEvents']
    )->name('v1.dashboard.events');
    Route::get('/v1/dashboard/eventsbydate/{eventAt}', [DashboardController::class, 'getEventsByDate']
    )->name('v1.dashboard.eventsbydate');

    Route::get('/v1/users', [UserController::class, 'index']
    )->name('v1.users');
    Route::get('/v1/users/create', [UserController::class, 'create']
    )->name('v1.users.create');
    Route::post('/v1/users/store', [UserController::class, 'store']
    )->name('v1.users.store');
    Route::get('/users/{emp_id}/edit', [UserController::class, 'edit'])->name('v1.users.edit');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('v1.users.update');

    Route::get('/v1/users/data', [UserController::class, 'getUsers'])->name('v1.users.data');
    Route::get('/v1/job-assignment-form', [JobAssignmentController::class, 'index'])->name('v1.job-assignment-form');
    Route::get('/v1/job-assignment-form/create', [JobAssignmentController::class, 'create'])->name('v1.job-assignment-form.create');
    Route::post('/v1/job-assignment-form/store', [JobAssignmentController::class, 'store'])->name('v1.job-assignment-form.store');
    Route::post('/v1/job-assignment-form/respond', [JobAssignmentController::class, 'respond'])->name('v1.job-assignment-form.respond');
    Route::get('/v1/job-assignment-form/list', [JobAssignmentController::class, 'list'])->name('v1.job-assignment-form.list');
    Route::get('/v1/job-assignment-form/view/{id}/{respond}', [JobAssignmentController::class, 'view'])->name('v1.job-assignment-form.view');
    Route::get('/v1/job-assignment-form/job-list', [JobAssignmentController::class, 'getJobsAssignments'])->name('v1.job-assignment-form.job-list');
    Route::get('/v1/job-assignment-form/job-list-user', [JobAssignmentController::class, 'getJobsAssignmentsByUser'])->name('v1.job-assignment-form.job-list-user');
    Route::get('/v1/roles', [RoleController::class, 'index'])->name('v1.roles');
    Route::get('/v1/roles/data', [RoleController::class, 'getRoles'])->name('v1.roles.data');
    Route::get('/v1/roles/create', [RoleController::class, 'create'])->name('v1.roles.create');
    Route::post('/v1/roles/store', [RoleController::class, 'store'])->name('v1.roles.store');
    Route::get('/v1/roles/{id}/show', [RoleController::class, 'show'])->name('v1.roles.show');
    Route::get('/v1/roles/{id}/edit', [RoleController::class, 'edit'])->name('v1.roles.edit');
    Route::put('/v1/roles/{id}', [RoleController::class, 'update'])->name('v1.roles.update');
    Route::post('/v1/roles/assign_permissions',[RoleController::class, 'assignPermissions'])->name('v1.roles.assign_permissions');

    Route::get('/v1/permissions', [PermissionController::class, 'index'])->name('v1.permissions');
    Route::get('/v1/permissions/data', [PermissionController::class, 'getPermissions'])->name('v1.permissions.data');
    Route::get('/v1/permissions/create', [PermissionController::class, 'create'])->name('v1.permissions.create');
    Route::post('/v1/permissions/store', [PermissionController::class, 'store'])->name('v1.permissions.store');


});

Route::post('/dologin', function (Request $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');

        if ($email == 'marketing@vib-tech.com.sg') {
            $request->session()->put('role', 'marketing');
            return redirect()->intended(route('dashboard'));
        } else if ($email == 'sales@vib-tech.com.sg') {
            $request->session()->put('role', 'sales');
            return redirect()->intended(route('dashboard'));
        } else if ($email == 'operations@vib-tech.com.sg') {
            $request->session()->put('role', 'operations');
            return redirect()->intended(route('dashboard'));
        } else if ($email == 'project@vib-tech.com.sg') {
            $request->session()->put('role', 'project');
            return redirect()->intended(route('dashboard'));
        } else if ($email == 'it@vib-tech.com.sg') {
            $request->session()->put('role', 'it');
            return redirect()->intended(route('dashboard'));
        } else if ($email == 'admin@vib-tech.com.sg') {
            $request->session()->put('role', 'admin');
            return redirect()->intended(route('dashboard'));
        } else {

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }
    })->name('dologin');

Route::post('/logout',function(Request $request) {
    $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login');

})->name('logout');

Route::get('/dashboard', function () {
    return view('dashboard',['title'=>'Dashboard','breadcrumb'=>['Home','Dashboard']]);
})->name('dashboard');

Route::get('/job-assignment-form', function () {
    return view('dummy',['title'=>'Job Assignment Form','breadcrumb'=>['Home','Staff Task','Job Assignment Form']]);
})->name('job-assignment-form');

Route::get('/leave-application', function () {
    return view('dummy',['title'=>'Leave Application','breadcrumb'=>['Home','Staff Task','Leave Application']]);
})->name('leave-application');

Route::get('/vehicle-booking', function () {
    return view('vehicle_booking',['title'=>'Vehicle Booking','breadcrumb'=>['Home','Staff Task','Vehicle Booking']]);
})->name('vehicle-booking');

Route::get('/vehicle-booking/create', function () {
    return view('vehicle_booking_create',['title'=>'Vehicle Booking','breadcrumb'=>['Home','Staff Task','Vehicle Booking','Create a Vehicle Booking']]);
})->name('vehicle-booking-create');

Route::get('/vehicle-booking/list', function () {
    return view('vehicle_booking_list',['title'=>'Vehicle Booking','breadcrumb'=>['Home','Staff Task','Vehicle Booking']]);
})->name('vehicle-booking-list');

Route::get('/vehicle-booking/view', function (Request $request) {
    return view('vehicle_booking_view',['title'=>'Vehicle Booking','breadcrumb'=>['Home','Staff Task','Vehicle Booking','Vehicle Booking List'],'id'=>$request->get('id')]);
})->name('vehicle-booking-view');

Route::get('/referral-program', function () {
    return view('dummy',['title'=>'Referral Program','breadcrumb'=>['Home','Staff Task','Referral Program']]);
})->name('referral-program');

Route::get('/submit-claim', function () {
    return view('submit_claim',['title'=>'Submit Claim','breadcrumb'=>['Home','Staff Task','Submit Claim']]);
})->name('submit-claim');

Route::get('/submit-claim/create', function () {
    return view('submit_claim_create',['title'=>'Submit Claim','breadcrumb'=>['Home','Staff Task','Submit Claim']]);
})->name('submit-claim-create');

Route::get('/submit-claim/view', function (Request $request) {
    return view('submit_claim_view',['title'=>'Submit Claim','breadcrumb'=>['Home','Staff Task','Submit Claim','Submit Claim Status'],'id'=>$request->get('id')]);
})->name('submit-claim-view');

Route::get('/submit-claim/list', function () {
    return view('submit_claim_list',['title'=>'Submit Claim','breadcrumb'=>['Home','Staff Task','Submit Claim']]);
})->name('submit-claim-list');

Route::get('/management-menu', function () {
    return view('dummy',['title'=>'Management Menu','breadcrumb'=>['Home','Staff Task','Management Menu']]);
})->name('management-menu');

Route::get('/management-memo', function () {
    return view('management_memo',['title'=>'Management Memo','breadcrumb'=>['Home','Staff Task','Management Memo']]);
})->name('management-memo');

Route::get('/employee-handbook', function () {
    return view('emp_handbook',['title'=>'Employee Handbook','breadcrumb'=>['Home','Staff Task','Employee Handbook']]);
})->name('employee-handbook');

Route::get('/whistleblowing-policy', function () {
    return view('whistleblowing_policy',['title'=>'Whistleblowing Policy','breadcrumb'=>['Home','Staff Task','Employee Handbook']]);
})->name('whistleblowing-policy');

Route::get('/profile', function () {
    $user = auth()->user();
    return view('profile',['title'=>'My Profile','breadcrumb'=>['Home','My Profile'],'user'=>$user]);
})->name('profile');

Route::get('/inbox', function () {
    return view('inbox',['title'=>'Inbox','breadcrumb'=>['Home','Inbox']]);
})->name('inbox');

Route::get('/shipping-status', function () {
    return view('shipping_status',['title'=>'Shipping/Delivery Status','breadcrumb'=>['Home','Admin Tools','Shipping/Delivery Status']]);
})->name('shipping-status');

Route::get('/shipping-status-create', function () {
    return view('shipping_status_create',['title'=>'Create New Order','breadcrumb'=>['Home','Admin Tools','Shipping/Delivery Status','Create New Order']]);
})->name('shipping-status-create');

Route::get('/shipping-status-view', function () {
    return view('shipping_status_view',['title'=>'Shipping/Delivery Status Detail','breadcrumb'=>['Home','Admin Tools','Shipping/Delivery Status','Existing Order']]);
})->name('shipping-status-view');

Route::get('/shipping-status-list', function () {
    return view('shipping_status_list',['title'=>'Shipping/Delivery Status List','breadcrumb'=>['Home','Admin Tools','Shipping/Delivery Status','Create New Order']]);
})->name('shipping-status-list');

Route::get('/shipping-status-history-list', function () {
    return view('shipping_status_history_list',['title'=>'Order History List','breadcrumb'=>['Home','Admin Tools','Shipping/Delivery Status','Order History']]);
})->name('shipping-status-history-list');

Route::get('/account-receivable-list', function () {
    return view('account_receivable_list',['title'=>'Account Receivable','breadcrumb'=>['Home','Admin Tools','Account Receivable']]);
})->name('account-receivable-list');
