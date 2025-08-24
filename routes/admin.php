<?php

use App\Http\Controllers\Employee\EmployeeActivityController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Admin\AdminLogin;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\DesignationController;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::view('/', 'admin.auth.admin_login')->name('home');
//Route::post('admin-login', AdminLogin::class)->middleware('throttle:5,1')->name('admin-login');
Route::post('admin-login', AdminLogin::class)->name('admin-login');
Route::view('permission-denied', 'admin.permission_denied')->name('permission-denied');

Route::group(['as' => 'admin.', 'middleware' => 'auth'], function () {
    // ===================================================================
    // ONLY FOR ADMIN ROUTES
    // ===================================================================
    Route::middleware('admin')->group(function () {
        // Dashboard
        Route::controller(DashboardController::class)->group(function () {
            Route::get('dashboard', 'dashboard')->name('dashboard');
        });
        // Profile
        Route::controller(ProfileController::class)->group(function () {
            Route::get('profile', 'profile')->name('profile');
            Route::put('update-profile', 'updateProfile')->name('update-profile');
            Route::view('mobile-verification', 'admin.profile.verify_mobile')->name('mobile-verification');
            Route::post('send-verification-code', 'sendVerificationCode')->name('send-verification-code');
            Route::post('verify-code', 'verifyCode')->name('verify-code');
        });
        // Hr configuration
        Route::resource('designations', DesignationController::class);
        Route::resource('departments', DepartmentController::class);
        Route::resource('branches', BranchController::class);
        // Employee/Staff
        Route::resource('staffs', StaffController::class);
        Route::controller(StaffController::class)->group(function () {
            Route::put('update-staff-status/{id}', 'updateStaffStatus')->name('update-staff-status');
        });
        // Attendance manage
        Route::controller(AttendanceController::class)->group(function() {
            Route::get('attendance-logs','attendanceLogs')->name('attendance-logs');
            Route::get('track-attendance-location','attendanceDetails')->name('track-attendance-location');
            Route::get('user-punch-history','punchHistory')->name('user-punch-history');
            Route::get('edit-attendance/{id}','editAttendance')->name('edit-attendance');
            Route::put('update-attendance/{id}','updateAttendance')->name('update-attendance');
        });
        // Settings
        Route::controller(SettingController::class)->group(function () {
            Route::get('site-settings', 'siteSettings')->name('site-settings');
            Route::put('update-site-settings', 'updateSiteSettings')->name('update-site-settings');
        });
    });
    // ===================================================================
    // ONLY FOR EMPLOYEE ROUTES
    // ===================================================================
    Route::controller(EmployeeActivityController::class)->middleware('employee')->group(function () {
        //Route::get('attendance-summery', 'attendanceSummery')->name('attendance-summery');
        Route::view('check-in-out','attendance.check_in_out')->name('check-in-out');
        Route::post('punch-manual', 'punchManual')->name('punch-manual');
        //Route::get('attendance-location/{id}','attendanceLocation')->name('attendance-location');
    });
});

// Logout
Route::get('logout', function () {
    Auth::logout();
    Session::reflash();
    return redirect()->route('home');
})->name('admin.logout');

Route::get('bulk-operation', function () {
    //
});
