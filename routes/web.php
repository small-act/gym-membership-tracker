<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DataTransferController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\MemberAccountController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\MembershipPlanController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StaffAccountController;
use App\Http\Controllers\SystemResetController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PayMongoWebhookController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('dashboard');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // Member portal account management: Admin and Super Admin only.
    Route::delete('/memberships/{membership}', [
    \App\Http\Controllers\MembershipController::class,
    'destroy',
])->name('memberships.destroy');

    Route::middleware('role:admin,super_admin')
        ->prefix('member-accounts')
        ->name('member-accounts.')
        ->group(function () {
            Route::get('/', [MemberAccountController::class, 'index'])->name('index');
            Route::post('/', [MemberAccountController::class, 'store'])->name('store');
            Route::delete('/{memberAccount}', [MemberAccountController::class, 'destroy'])->name('destroy');
        });
    // Members
    Route::get('/members', [MemberController::class, 'index'])
        ->name('members.index');

    Route::get('/members/create', [MemberController::class, 'create'])
        ->name('members.create');

    Route::post('/members', [MemberController::class, 'store'])
        ->name('members.store');
Route::delete('/members/{member}', [MemberController::class, 'destroy'])
    ->name('members.destroy');

    Route::get('/members/{member}/edit', [MemberController::class, 'edit'])
        ->name('members.edit');

    Route::put('/members/{member}', [MemberController::class, 'update'])
        ->name('members.update');

    // Membership Plans
    Route::get('/membership-plans', [MembershipPlanController::class, 'index'])
        ->name('membership-plans.index');

    Route::get('/membership-plans/create', [MembershipPlanController::class, 'create'])
        ->name('membership-plans.create');

    Route::post('/membership-plans', [MembershipPlanController::class, 'store'])
        ->name('membership-plans.store');

    Route::get('/membership-plans/{membershipPlan}/edit', [MembershipPlanController::class, 'edit'])
        ->name('membership-plans.edit');

    Route::put('/membership-plans/{membershipPlan}', [MembershipPlanController::class, 'update'])
        ->name('membership-plans.update');

    // Membership Registration and Renewal
    Route::get('/memberships', [MembershipController::class, 'index'])
        ->name('memberships.index');

    Route::get('/memberships/create', [MembershipController::class, 'create'])
        ->name('memberships.create');

    Route::post('/memberships', [MembershipController::class, 'store'])
        ->name('memberships.store');

    Route::post('/memberships/{membership}/renew', [MembershipController::class, 'renew'])
        ->name('memberships.renew');

    // Attendance
    Route::get('/attendance', [AttendanceController::class, 'index'])
        ->name('attendance.index');

    Route::post('/attendance/check-in', [AttendanceController::class, 'checkIn'])
        ->name('attendance.check-in');

    Route::post('/attendance/qr-check-in', [AttendanceController::class, 'qrCheckIn'])
        ->name('attendance.qr-check-in');

    Route::post('/attendance/{attendance}/check-out', [AttendanceController::class, 'checkOut'])
        ->name('attendance.check-out');

    // Payments
    Route::get('/payments', [PaymentController::class, 'index'])
        ->name('payments.index');

    Route::get('/payments/create', [PaymentController::class, 'create'])
        ->name('payments.create');

    Route::post('/payments', [PaymentController::class, 'store'])
        ->name('payments.store');

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])
        ->name('reports.index');

    Route::get('/reports/export', [ReportController::class, 'export'])
        ->name('reports.export');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

    /*
    |--------------------------------------------------------------------------
    | Super Admin: Staff Management
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:super_admin')
        ->prefix('staff')
        ->name('staff.')
        ->group(function () {

            // Staff list and account creation
            Route::get('/', [StaffAccountController::class, 'index'])
                ->name('index');

            Route::post('/', [StaffAccountController::class, 'store'])
                ->name('store');

            // Change a staff member's role
            Route::patch('/{user}/role', [StaffAccountController::class, 'updateRole'])
                ->name('role.update');

            // Set a specific password
            Route::patch('/{user}/reset-password', [StaffAccountController::class, 'resetPassword'])
                ->name('reset-password');

            // Staff password management page
            Route::get('/passwords', [StaffAccountController::class, 'passwords'])
                ->name('passwords');

            // Generate and set a new password
            Route::patch('/{user}/generate-password', [
                StaffAccountController::class,
                'resetGeneratedPassword',
            ])->name('generate-password');

            // Delete staff accounts
            Route::delete('/delete-all', [StaffAccountController::class, 'destroyAll'])
                ->name('delete-all');

            Route::delete('/{user}', [StaffAccountController::class, 'destroy'])
                ->name('destroy');
        });

    /*
    |--------------------------------------------------------------------------
    | Admin and Super Admin: Cashier Password Reset
    |--------------------------------------------------------------------------
    | These routes set a new password. They do not retrieve an old password.
    */

    Route::middleware('role:admin,super_admin')
        ->prefix('cashier-passwords')
        ->name('cashier-passwords.')
        ->group(function () {

            Route::get('/', function () {
                $users = User::whereIn('role', ['cashier'])
                    ->orderBy('name')
                    ->paginate(15);

                return view('staff.reset-cashier-password', compact('users'));
            })->name('index');

            Route::patch('/{user}', function (Request $request, User $user) {

                abort_unless(
                    in_array($user->role, ['cashier'], true),
                    404
                );

                $validated = $request->validate([
                    'password' => ['required', 'string', 'min:12', 'confirmed'],
                ]);

                $user->update([
                    'password' => Hash::make($validated['password']),
                ]);

                return redirect()
                    ->route('cashier-passwords.index')
                    ->with('success', 'Cashier password reset successfully.');
            })->name('update');
        });

    /*
    |--------------------------------------------------------------------------
    | Super Admin: System Reset
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:super_admin')
        ->prefix('system-reset')
        ->name('system-reset.')
        ->group(function () {

            Route::get('/', [SystemResetController::class, 'index'])
                ->name('index');

            Route::post('/', [SystemResetController::class, 'store'])
                ->name('store');
        });

    /*
   
    /*
    |--------------------------------------------------------------------------
    | Admin and Super Admin: Data Import and Export
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:admin,super_admin')
        ->prefix('data-transfer')
        ->name('data-transfer.')
        ->group(function () {
            Route::get('/', [DataTransferController::class, 'index'])
                ->name('index');

            Route::get('/export', [DataTransferController::class, 'export'])
                ->name('export');

            Route::post('/import', [DataTransferController::class, 'import'])
                ->name('import');
        });

    /*
    |--------------------------------------------------------------------------
    | Super Admin: Membership Management
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:super_admin')->group(function () {
        Route::get('/memberships/{membership}/edit', [
            MembershipController::class,
            'edit',
        ])->name('memberships.edit');

        Route::put('/memberships/{membership}', [
            MembershipController::class,
            'update',
        ])->name('memberships.update');

            });
});

/*
|--------------------------------------------------------------------------
| PayMongo Webhook
|--------------------------------------------------------------------------
| This route must be outside the authenticated route group.
*/

Route::post('/paymongo/webhook', [
    PayMongoWebhookController::class,
    'handle',
])->name('paymongo.webhook');

/*
|--------------------------------------------------------------------------
| Authentication and Additional Member Routes
|--------------------------------------------------------------------------
*/

require __DIR__ . '/auth.php';
require __DIR__ . '/member.php';
