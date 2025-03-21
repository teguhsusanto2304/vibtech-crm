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
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\JobAssignmentController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PositionLevelController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VehicleBookingController;
use App\Http\Controllers\VehicleController;

Route::get('/', function () {
    return view('login');
});

Route::get('/login', function () {
    return view('login');
})->name('login');

Route::get('/v1/login', [LoginController::class, 'showLoginForm'])->name('v1.login');
Route::post('/v1/login', [LoginController::class, 'login'])->name('v1.login');

Route::get('/clear-cache', function () {
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    Artisan::call('route:clear');
    Artisan::call('event:clear');
    Artisan::call('package:discover --ansi');
    return "Cache cleared successfully!";
});


Route::middleware('auth')->group(function () {
    Route::get('/notifications', [NotificationController::class, 'getNotifications'])->name('notifications.get');
    Route::post('/notifications/read', [NotificationController::class, 'markAllRead'])->name('notifications.read');



    Route::prefix('v1')->group(function () {

        // 🔹 Dashboard Routes
        Route::prefix('dashboard')->controller(DashboardController::class)->group(function () {
            Route::get('/', 'index')->name('v1.dashboard');
            Route::get('/events', 'getEvents')->name('v1.dashboard.events');
            Route::get('/eventsbydate/{eventAt}', 'getEventsByDate')->name('v1.dashboard.eventsbydate');
        });

        // 🔹 User Management Routes
        Route::prefix('users')->controller(UserController::class)->group(function () {
            Route::get('/', 'index')->name('v1.users');
            Route::get('/create', 'create')->name('v1.users.create');
            Route::post('/store', 'store')->name('v1.users.store');
            Route::get('/{emp_id}/edit', 'edit')->name('v1.users.edit');
            Route::put('/{id}', 'update')->name('v1.users.update');
            Route::get('/data', 'getUsers')->name('v1.users.data');
            Route::post('/toggle-status','toggleStatus')->name('v1.users.toggle-status');
        });

        // Group routes for JobAssignmentController
        Route::prefix('job-assignment-form')->controller(JobAssignmentController::class)->group(function () {
            Route::get('/', 'index')->name('v1.job-assignment-form');
            Route::get('/create', 'create')->name('v1.job-assignment-form.create');
            Route::post('/store', 'store')->name('v1.job-assignment-form.store');
            Route::post('/respond', 'respond')->name('v1.job-assignment-form.respond');
            Route::get('/list', 'list')->name('v1.job-assignment-form.list');
            Route::get('/view/{id}/{respond}', 'view')->name('v1.job-assignment-form.view');
            Route::get('/job-list', 'getJobsAssignments')->name('v1.job-assignment-form.job-list');
            Route::get('/job-list-user', 'getJobsAssignmentsByUser')->name('v1.job-assignment-form.job-list-user');
            Route::get('/invited-staff/{user_id}/{job_id}', 'invitedStaff')->name('v1.job-assignment-form.job.invited-staff');
            Route::get('/history', 'history')->name('v1.job-assignment-form.history');
            Route::get('/job-list/history','getJobsAssignmentHistories')->name('v1.job-assignment-form.history.data');
            Route::post('/update-status','updateJobAssignmentStatus')->name('v1.job-assignment-form.history.update-status');
            Route::post('/update-vehicle-require','updateJobAssignmentVehicleRequire')->name('v1.job-assignment-form.update-vehicle-require');
            Route::get('/{id}/edit','edit')->name('v1.job-assignment-form.edit');
            Route::put('{id}',  'update')->name('v1.job-assignment-form.update');
            Route::get('/send-email','sendBookingEmail')->name('v1.job-assignment-form.send-email');
            Route::post('/assign-vehicle-booker', 'assignVehicleBooker')->name('v1.job-assignment-form.assign-vehicle-booker');
        });

        // 🔹 Role Management Routes
        Route::prefix('roles')->controller(RoleController::class)->group(function () {
            Route::get('/', 'index')->name('v1.roles');
            Route::get('/data', 'getRoles')->name('v1.roles.data');
            Route::get('/create', 'create')->name('v1.roles.create');
            Route::post('/store', 'store')->name('v1.roles.store');
            Route::get('/{id}/show', 'show')->name('v1.roles.show');
            Route::get('/{id}/edit', 'edit')->name('v1.roles.edit');
            Route::put('/{id}', 'update')->name('v1.roles.update');
            Route::put('/{id}/delete', 'destroy')->name('v1.roles.destroy');
            Route::post('/assign_permissions', 'assignPermissions')->name('v1.roles.assign_permissions');
        });

        // 🔹 Permission Management Routes
        Route::prefix('permissions')->controller(PermissionController::class)->group(function () {
            Route::get('/', 'index')->name('v1.permissions');
            Route::get('/data', 'getPermissions')->name('v1.permissions.data');
            Route::get('/create', 'create')->name('v1.permissions.create');
            Route::post('/store', 'store')->name('v1.permissions.store');
        });

        // 🔹 Department Management Routes
        Route::prefix('departments')->controller(DepartmentController::class)->group(function () {
            Route::get('/', 'index')->name('v1.departments');
            Route::get('/data', 'getDepartments')->name('v1.departments.data');
            Route::get('/create', 'create')->name('v1.departments.create');
            Route::post('/store', 'store')->name('v1.departments.store');
        });

        // 🔹 Position Level Routes
        Route::prefix('position-levels')->controller(PositionLevelController::class)->group(function () {
            Route::get('/', 'index')->name('v1.position-levels');
            Route::get('/data', 'getPositionLevels')->name('v1.position-levels.data');
            Route::get('/create', 'create')->name('v1.position-levels.create');
            Route::post('/store', 'store')->name('v1.position-levels.store');
        });

        // 🔹 Vehicle Booking Routes
        Route::prefix('vehicle-bookings')->controller(VehicleBookingController::class)->group(function () {
            Route::get('/', 'index')->name('v1.vehicle-bookings');
            Route::get('/create', 'create')->name('v1.vehicle-bookings.create');
            Route::get('/{id}/edit', 'edit')->name('v1.vehicle-bookings.edit');
            Route::post('/store', 'store')->name('v1.vehicle-bookings.store');
            Route::put('/{id}/update', 'update')->name('v1.vehicle-bookings.update');
            Route::put('/{id}/cancel', 'cancel')->name('v1.vehicle-bookings.cancel');
            Route::get('/list', 'list')->name('v1.vehicle-bookings.list');
            Route::get('/data','getData')->name('v1.vehicle-bookings.data');
            Route::get('/{id}/detail','show')->name('v1.vehicle-bookings.detail');
            Route::get('/{id}/modal', 'commonShow')->name('v1.vehicle-bookings.modal');
            Route::get('/available-vehicles','getAvailableVehicles')->name('v1.vehicle-bookings.available-vehicles');
        });

        // 🔹 Vehicles Routes
        Route::prefix('vehicles')->controller(VehicleController::class)->group(function () {
            Route::get('/create', 'create')->name('v1.vehicles.create');
            Route::get('/{id}/edit', 'edit')->name('v1.vehicles.edit');
            Route::post('/store', 'store')->name('v1.vehicles.store');
            Route::put('/{id}/delete', 'delete')->name('v1.vehicles.delete');
            Route::put('/{id}/update', 'update')->name('v1.vehicles.update');
            Route::get('/list', 'list')->name('v1.vehicles.list');
            Route::get('/car-image', 'getCarImages')->name('v1.vehicles.car-image');
            Route::get('/data','getData')->name('v1.vehicle.data');

        });


    });





});

Route::post('/dologin', function (Request $request) {
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

Route::post('/logout', function (Request $request) {
    $request->session()->invalidate();

    $request->session()->regenerateToken();

    return redirect()->route('login');

})->name('logout');

Route::get('/dashboard', function () {
    return view('dashboard', ['title' => 'Dashboard', 'breadcrumb' => ['Home', 'Dashboard']]);
})->name('dashboard');

Route::get('/job-assignment-form', function () {
    return view('dummy', ['title' => 'Job Assignment Form', 'breadcrumb' => ['Home', 'Staff Task', 'Job Assignment Form']]);
})->name('job-assignment-form');

Route::get('/leave-application', function () {
    return view('dummy', ['title' => 'Leave Application', 'breadcrumb' => ['Home', 'Staff Task', 'Leave Application']]);
})->name('leave-application');

Route::get('/vehicle-booking', function () {
    return view('vehicle_booking', ['title' => 'Vehicle Booking', 'breadcrumb' => ['Home', 'Staff Task', 'Vehicle Booking']]);
})->name('vehicle-booking');

Route::get('/vehicle-booking/create', function () {
    return view('vehicle_booking_create', ['title' => 'Vehicle Booking', 'breadcrumb' => ['Home', 'Staff Task', 'Vehicle Booking', 'Create a Vehicle Booking']]);
})->name('vehicle-booking-create');

Route::get('/vehicle-booking/list', function () {
    return view('vehicle_booking_list', ['title' => 'Vehicle Booking', 'breadcrumb' => ['Home', 'Staff Task', 'Vehicle Booking']]);
})->name('vehicle-booking-list');

Route::get('/vehicle-booking/view', function (Request $request) {
    return view('vehicle_booking_view', ['title' => 'Vehicle Booking', 'breadcrumb' => ['Home', 'Staff Task', 'Vehicle Booking', 'Vehicle Booking List'], 'id' => $request->get('id')]);
})->name('vehicle-booking-view');

Route::get('/referral-program', function () {
    return view('dummy', ['title' => 'Referral Program', 'breadcrumb' => ['Home', 'Staff Task', 'Referral Program']]);
})->name('referral-program');

Route::get('/submit-claim', function () {
    return view('submit_claim', ['title' => 'Submit Claim', 'breadcrumb' => ['Home', 'Staff Task', 'Submit Claim']]);
})->name('submit-claim');

Route::get('/submit-claim/create', function () {
    return view('submit_claim_create', ['title' => 'Submit Claim', 'breadcrumb' => ['Home', 'Staff Task', 'Submit Claim']]);
})->name('submit-claim-create');

Route::get('/submit-claim/view', function (Request $request) {
    return view('submit_claim_view', ['title' => 'Submit Claim', 'breadcrumb' => ['Home', 'Staff Task', 'Submit Claim', 'Submit Claim Status'], 'id' => $request->get('id')]);
})->name('submit-claim-view');

Route::get('/submit-claim/list', function () {
    return view('submit_claim_list', ['title' => 'Submit Claim', 'breadcrumb' => ['Home', 'Staff Task', 'Submit Claim']]);
})->name('submit-claim-list');

Route::get('/management-menu', function () {
    return view('dummy', ['title' => 'Management Menu', 'breadcrumb' => ['Home', 'Staff Task', 'Management Menu']]);
})->name('management-menu');

Route::get('/management-memo', function () {
    return view('management_memo', ['title' => 'Management Memo', 'breadcrumb' => ['Home', 'Staff Task', 'Management Memo']]);
})->name('management-memo');

Route::get('/employee-handbook', function () {
    return view('emp_handbook', ['title' => 'Employee Handbook', 'breadcrumb' => ['Home', 'Staff Task', 'Employee Handbook']]);
})->name('employee-handbook');

Route::get('/whistleblowing-policy', function () {
    return view('whistleblowing_policy', ['title' => 'Whistleblowing Policy', 'breadcrumb' => ['Home', 'Staff Task', 'Employee Handbook']]);
})->name('whistleblowing-policy');

Route::get('/profile', function () {
    $user = auth()->user();
    return view('profile', ['title' => 'My Profile', 'breadcrumb' => ['Home', 'My Profile'], 'user' => $user]);
})->name('profile');

Route::get('/profile/change-password', function () {
    return view('change_password', ['title' => 'Change Password', 'breadcrumb' => ['Home', 'My Profile', 'Change Password']]);
})->name('profile.change-password');

Route::post('/profile/password-update', [ProfileController::class, 'updatePassword'])->name('profile.password-update');

Route::get('/inbox', function () {
    return view('chat.inbox', ['title' => 'Inbox', 'breadcrumb' => ['Home', 'Inbox']]);
})->name('inbox');

Route::get('/shipping-status', function () {
    return view('shipping_status', ['title' => 'Shipping/Delivery Status', 'breadcrumb' => ['Home', 'Admin Tools', 'Shipping/Delivery Status']]);
})->name('shipping-status');

Route::get('/shipping-status-create', function () {
    return view('shipping_status_create', ['title' => 'Create New Order', 'breadcrumb' => ['Home', 'Admin Tools', 'Shipping/Delivery Status', 'Create New Order']]);
})->name('shipping-status-create');

Route::get('/shipping-status-view', function () {
    return view('shipping_status_view', ['title' => 'Shipping/Delivery Status Detail', 'breadcrumb' => ['Home', 'Admin Tools', 'Shipping/Delivery Status', 'Existing Order']]);
})->name('shipping-status-view');

Route::get('/shipping-status-list', function () {
    return view('shipping_status_list', ['title' => 'Shipping/Delivery Status List', 'breadcrumb' => ['Home', 'Admin Tools', 'Shipping/Delivery Status', 'Create New Order']]);
})->name('shipping-status-list');

Route::get('/shipping-status-history-list', function () {
    return view('shipping_status_history_list', ['title' => 'Order History List', 'breadcrumb' => ['Home', 'Admin Tools', 'Shipping/Delivery Status', 'Order History']]);
})->name('shipping-status-history-list');

Route::get('/account-receivable-list', function () {
    return view('account_receivable_list', ['title' => 'Account Receivable', 'breadcrumb' => ['Home', 'Admin Tools', 'Account Receivable']]);
})->name('account-receivable-list');
