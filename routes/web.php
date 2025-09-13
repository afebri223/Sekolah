<?php
// routes/web.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AttendanceController;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/', function () {
        return view('welcome');
    });

    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Admin routes
    Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'adminDashboard'])->name('dashboard');

        // User Management
        Route::resource('users', App\Http\Controllers\Admin\UserManagementController::class);

        // Class Management
        Route::resource('classes', App\Http\Controllers\Admin\ClassManagementController::class);

        // Student Management
        Route::resource('students', App\Http\Controllers\Admin\StudentManagementController::class);
        Route::post('students/bulk-import', [App\Http\Controllers\Admin\StudentManagementController::class, 'bulkImport'])
            ->name('students.bulk-import');

        // Attendance Reports
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/attendance', [App\Http\Controllers\Admin\AttendanceReportController::class, 'index'])
                ->name('attendance');
            Route::get('/attendance/export', [App\Http\Controllers\Admin\AttendanceReportController::class, 'export'])
                ->name('attendance.export');
            Route::get('/attendance/summary', [App\Http\Controllers\Admin\AttendanceReportController::class, 'summary'])
                ->name('attendance.summary');
        });
    });

    // Kepala Sekolah routes
    Route::middleware('role:kepala_sekolah')->group(function () {
        Route::get('/kepala-sekolah/dashboard', [DashboardController::class, 'kepalaSekolahDashboard'])
            ->name('kepala-sekolah.dashboard');
    });

    // Guru routes
    Route::middleware('role:guru')->group(function () {
        Route::get('/guru/dashboard', [DashboardController::class, 'guruDashboard'])
            ->name('guru.dashboard');

        // Wali kelas routes
        Route::middleware('wali_kelas')->group(function () {
            Route::get('/wali-kelas/dashboard', [DashboardController::class, 'waliKelasDashboard'])
                ->name('wali-kelas.dashboard');

            Route::get('/attendance', [AttendanceController::class, 'index'])
                ->name('attendance.index');
            Route::post('/attendance', [AttendanceController::class, 'store'])
                ->name('attendance.store');
            Route::get('/attendance/history', [AttendanceController::class, 'history'])
                ->name('attendance.history');
        });
    });

    // Fallback dashboard
    Route::get('/dashboard', function () {
        return redirect()->route('guru.dashboard');
    })->name('dashboard');
});
