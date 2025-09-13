<?php
// routes/web.php - Simple Complete Routes

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\ClassManagementController;
use App\Http\Controllers\Admin\StudentManagementController;
use App\Http\Controllers\Admin\AttendanceReportController;

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
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'adminDashboard'])->name('dashboard');
        
        // User Management CRUD
        Route::resource('users', UserManagementController::class);
        
        // Class Management CRUD
        Route::resource('classes', ClassManagementController::class);
        
        // Student Management CRUD
        Route::resource('students', StudentManagementController::class);
        Route::post('students/bulk-import', [StudentManagementController::class, 'bulkImport'])->name('students.bulk-import');
        
        // Reports
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/attendance', [AttendanceReportController::class, 'index'])->name('attendance');
            Route::get('/attendance/summary', [AttendanceReportController::class, 'summary'])->name('attendance.summary');
            Route::get('/attendance/daily', [AttendanceReportController::class, 'dailyReport'])->name('attendance.daily');
            Route::get('/attendance/export', [AttendanceReportController::class, 'export'])->name('attendance.export');
        });
    });
    
    // Kepala Sekolah routes
    Route::middleware('role:kepala_sekolah')->prefix('kepala-sekolah')->name('kepala-sekolah.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'kepalaSekolahDashboard'])->name('dashboard');
        
        // View only access to reports
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/attendance', [AttendanceReportController::class, 'index'])->name('attendance');
            Route::get('/attendance/summary', [AttendanceReportController::class, 'summary'])->name('attendance.summary');
            Route::get('/attendance/daily', [AttendanceReportController::class, 'dailyReport'])->name('attendance.daily');
        });
        
        // View classes (read-only)
        Route::get('/classes', function () {
            $classes = \App\Models\Classes::with(['waliKelas', 'students'])->paginate(15);
            return view('kepala-sekolah.classes.index', compact('classes'));
        })->name('classes.index');
        
        Route::get('/classes/{class}', function (\App\Models\Classes $class) {
            $class->load(['waliKelas', 'students']);
            return view('kepala-sekolah.classes.show', compact('class'));
        })->name('classes.show');
        
        // View students (read-only)
        Route::get('/students', function () {
            $students = \App\Models\Student::with('class')->where('status', 'active')->paginate(20);
            return view('kepala-sekolah.students.index', compact('students'));
        })->name('students.index');
    });
    
    // Guru routes
    Route::middleware('role:guru')->prefix('guru')->name('guru.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'guruDashboard'])->name('dashboard');
        
        // Profile management
        Route::get('/profile', function () {
            return view('guru.profile', ['user' => auth()->user()]);
        })->name('profile');
        
        Route::put('/profile', function (\Illuminate\Http\Request $request) {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . auth()->id(),
                'phone' => 'nullable|string|max:15',
                'password' => 'nullable|min:6|confirmed',
            ]);
            
            $data = $request->only(['name', 'email', 'phone']);
            if ($request->filled('password')) {
                $data['password'] = \Hash::make($request->password);
            }
            
            auth()->user()->update($data);
            return redirect()->route('guru.profile')->with('success', 'Profile berhasil diperbarui!');
        })->name('profile.update');
        
        // Wali kelas specific routes
        Route::middleware('wali_kelas')->prefix('wali-kelas')->name('wali-kelas.')->group(function () {
            Route::get('/dashboard', [DashboardController::class, 'waliKelasDashboard'])->name('dashboard');
            
            // Attendance Management
            Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
            Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
            Route::get('/attendance/history', [AttendanceController::class, 'history'])->name('attendance.history');
            
            // Students in class
            Route::get('/students', function () {
                $user = auth()->user();
                $class = $user->waliKelas;
                if (!$class) abort(403, 'Anda bukan wali kelas.');
                
                $students = $class->students()->where('status', 'active')->get();
                return view('wali-kelas.students.index', compact('class', 'students'));
            })->name('students.index');
            
            Route::get('/students/{student}', function (\App\Models\Student $student) {
                $user = auth()->user();
                if (!$user->waliKelas || $student->class_id !== $user->waliKelas->id) {
                    abort(403, 'Anda tidak memiliki akses ke siswa ini.');
                }
                
                $student->load(['attendances' => function($query) {
                    $query->latest()->limit(20);
                }]);
                
                return view('wali-kelas.students.show', compact('student'));
            })->name('students.show');
            
            // Monthly report
            Route::get('/reports/monthly', function (\Illuminate\Http\Request $request) {
                $user = auth()->user();
                $class = $user->waliKelas;
                if (!$class) abort(403);
                
                $month = $request->get('month', date('m'));
                $year = $request->get('year', date('Y'));
                
                $students = $class->students()->where('status', 'active')->get();
                $summary = [];
                
                foreach ($students as $student) {
                    $attendances = \App\Models\Attendance::where('student_id', $student->id)
                        ->whereMonth('date', $month)
                        ->whereYear('date', $year)
                        ->get();
                    
                    $summary[] = [
                        'student' => $student,
                        'hadir' => $attendances->where('status', 'hadir')->count(),
                        'izin' => $attendances->where('status', 'izin')->count(),
                        'sakit' => $attendances->where('status', 'sakit')->count(),
                        'alpha' => $attendances->where('status', 'alpha')->count(),
                        'total_days' => $attendances->count(),
                    ];
                }
                
                return view('wali-kelas.reports.monthly', compact('class', 'summary', 'month', 'year'));
            })->name('reports.monthly');
        });
    });
    
    // Fallback dashboard route
    Route::get('/dashboard', function () {
        $user = auth()->user();
        
        switch ($user->role) {
            case 'admin':
                return redirect()->route('admin.dashboard');
            case 'kepala_sekolah':
                return redirect()->route('kepala-sekolah.dashboard');
            case 'guru':
                if ($user->isWaliKelas()) {
                    return redirect()->route('wali-kelas.dashboard');
                }
                return redirect()->route('guru.dashboard');
            default:
                return redirect()->route('login');
        }
    })->name('dashboard');
});

// API Routes untuk AJAX
Route::middleware('auth')->prefix('api')->name('api.')->group(function () {
    // Get class students
    Route::get('/classes/{class}/students', function (\App\Models\Classes $class) {
        return $class->students()->where('status', 'active')->get();
    })->name('classes.students');
    
    // Dashboard stats
    Route::get('/stats/dashboard', function () {
        $user = auth()->user();
        
        if ($user->isAdmin()) {
            return [
                'users' => \App\Models\User::count(),
                'classes' => \App\Models\Classes::count(),
                'students' => \App\Models\Student::where('status', 'active')->count(),
                'attendance_today' => \App\Models\Attendance::whereDate('date', today())->count(),
            ];
        }
        
        if ($user->isWaliKelas()) {
            $class = $user->waliKelas;
            return [
                'total_students' => $class->students()->where('status', 'active')->count(),
                'attendance_today' => $class->attendances()->whereDate('date', today())->count(),
                'hadir_today' => $class->attendances()->whereDate('date', today())->where('status', 'hadir')->count(),
            ];
        }
        
        return [];
    })->name('stats.dashboard');
});

// Utility routes
Route::get('/health', function () {
    return [
        'status' => 'ok',
        'timestamp' => now(),
        'version' => '1.0.0'
    ];
});

// Error handling
Route::fallback(function () {
    return view('errors.404');
});

// Development routes (local environment only)
if (app()->environment('local')) {
    Route::prefix('dev')->group(function () {
        Route::get('/seed', function () {
            \Artisan::call('db:seed');
            return 'Database seeded successfully!';
        });
        
        Route::get('/clear', function () {
            \Artisan::call('cache:clear');
            \Artisan::call('config:clear');
            \Artisan::call('view:clear');
            \Artisan::call('route:clear');
            return 'All cache cleared!';
        });
    });
}