<?php
// routes/web.php - Enhanced with role-specific features

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\KepalaSekolahReportController;
use App\Http\Controllers\GuruReportController;
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
    
    // Admin routes - Full access
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'adminDashboard'])->name('dashboard');
        
        // User Management CRUD
        Route::resource('users', UserManagementController::class);
        
        // Class Management CRUD
        Route::resource('classes', ClassManagementController::class);
        
        // Student Management CRUD
        Route::resource('students', StudentManagementController::class);
        Route::post('students/bulk-import', [StudentManagementController::class, 'bulkImport'])->name('students.bulk-import');
        
        // Full Reports Access
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/attendance', [AttendanceReportController::class, 'index'])->name('attendance');
            Route::get('/attendance/summary', [AttendanceReportController::class, 'summary'])->name('attendance.summary');
            Route::get('/attendance/daily', [AttendanceReportController::class, 'dailyReport'])->name('attendance.daily');
            Route::get('/attendance/monthly', [AttendanceReportController::class, 'monthlyReport'])->name('attendance.monthly');
            Route::get('/attendance/yearly', [AttendanceReportController::class, 'yearlyReport'])->name('attendance.yearly');
            Route::get('/attendance/export', [AttendanceReportController::class, 'export'])->name('attendance.export');
        });
    });
    
    // KEPALA SEKOLAH - Enhanced Report Access with Filters
    Route::middleware('role:kepala_sekolah')->prefix('kepala-sekolah')->name('kepala-sekolah.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'kepalaSekolahDashboard'])->name('dashboard');
        
        // Enhanced Reports with Advanced Filtering
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [KepalaSekolahReportController::class, 'index'])->name('index');
            
            // Daily Reports with Class Filter
            Route::get('/daily', [KepalaSekolahReportController::class, 'dailyReport'])->name('daily');
            
            // Monthly Reports with Class Filter  
            Route::get('/monthly', [KepalaSekolahReportController::class, 'monthlyReport'])->name('monthly');
            
            // Yearly Reports with Class Performance Ranking
            Route::get('/yearly', [KepalaSekolahReportController::class, 'yearlyReport'])->name('yearly');
            
            // Export functionality
            Route::post('/export', [KepalaSekolahReportController::class, 'exportReport'])->name('export');
        });
        
        // Read-only access to class and student data
        Route::get('/classes', function () {
            $classes = \App\Models\Classes::with(['waliKelas', 'students' => function($query) {
                $query->where('status', 'active');
            }])->orderBy('grade')->orderBy('name')->paginate(15);
            
            return view('kepala-sekolah.classes.index', compact('classes'));
        })->name('classes.index');
        
        Route::get('/classes/{class}', function (\App\Models\Classes $class) {
            $class->load(['waliKelas', 'students' => function($query) {
                $query->where('status', 'active');
            }]);
            
            // Get recent attendance summary for this class
            $recentAttendance = \App\Models\Attendance::where('class_id', $class->id)
                ->whereDate('date', '>=', now()->subDays(30))
                ->selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status');
                
            return view('kepala-sekolah.classes.show', compact('class', 'recentAttendance'));
        })->name('classes.show');
        
        Route::get('/students', function (\Illuminate\Http\Request $request) {
            $query = \App\Models\Student::with(['class'])->where('status', 'active');
            
            // Filter by class if specified
            if ($request->filled('class_id')) {
                $query->where('class_id', $request->class_id);
            }
            
            // Search functionality
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('nis', 'like', "%{$search}%");
                });
            }
            
            $students = $query->orderBy('name')->paginate(20);
            $classes = \App\Models\Classes::orderBy('grade')->orderBy('name')->get();
            
            return view('kepala-sekolah.students.index', compact('students', 'classes'));
        })->name('students.index');
    });
    
    // GURU BIASA - Limited Report Access (Daily & Monthly only)
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
                'password' => 'nullable|min:8|confirmed',
            ]);
            
            $data = $request->only(['name', 'email', 'phone']);
            if ($request->filled('password')) {
                $data['password'] = \Hash::make($request->password);
            }
            
            auth()->user()->update($data);
            return redirect()->route('guru.profile')->with('success', 'Profile berhasil diperbarui!');
        })->name('profile.update');
        
        // LIMITED Report Access - Only Daily & Monthly
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [GuruReportController::class, 'index'])->name('index');
            Route::get('/daily', [GuruReportController::class, 'dailyReport'])->name('daily');
            Route::get('/monthly', [GuruReportController::class, 'monthlyReport'])->name('monthly');
            Route::get('/class/{class}', [GuruReportController::class, 'classDetail'])->name('class.detail');
        });
    });
    
    // WALI KELAS - Enhanced Attendance Management
    Route::middleware(['role:guru', 'wali_kelas'])->prefix('wali-kelas')->name('wali-kelas.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'waliKelasDashboard'])->name('dashboard');
        
        // ENHANCED Attendance Management
        Route::prefix('attendance')->name('attendance.')->group(function () {
            Route::get('/', [AttendanceController::class, 'index'])->name('index');
            Route::post('/', [AttendanceController::class, 'store'])->name('store');
            Route::get('/history', [AttendanceController::class, 'history'])->name('history');
            
            // Quick Mark for individual students (AJAX)
            Route::post('/quick-mark', [AttendanceController::class, 'quickMark'])->name('quick-mark');
        });
        
        // Students Management in Class
        Route::prefix('students')->name('students.')->group(function () {
            Route::get('/', function () {
                $user = auth()->user();
                $class = $user->waliKelas;
                
                if (!$class) {
                    return redirect()->route('guru.dashboard')
                        ->with('error', 'Anda bukan wali kelas.');
                }
                
                $students = $class->students()->where('status', 'active')
                    ->with(['attendances' => function($query) {
                        $query->whereDate('date', '>=', now()->subDays(7))
                              ->orderBy('date', 'desc');
                    }])
                    ->orderBy('name')
                    ->get();
                    
                // Calculate recent attendance stats for each student
                $students = $students->map(function($student) {
                    $recentAttendances = $student->attendances;
                    $student->recent_hadir = $recentAttendances->where('status', 'hadir')->count();
                    $student->recent_total = $recentAttendances->count();
                    $student->recent_rate = $student->recent_total > 0 
                        ? round(($student->recent_hadir / $student->recent_total) * 100, 1) 
                        : 0;
                    return $student;
                });
                
                return view('wali-kelas.students.index', compact('class', 'students'));
            })->name('index');
            
            Route::get('/{student}', function (\App\Models\Student $student) {
                $user = auth()->user();
                if (!$user->waliKelas || $student->class_id !== $user->waliKelas->id) {
                    abort(403, 'Anda tidak memiliki akses ke siswa ini.');
                }
                
                // Get detailed attendance history
                $student->load(['attendances' => function($query) {
                    $query->with('recordedBy')->latest()->limit(50);
                }]);
                
                // Monthly statistics for current academic year
                $monthlyStats = [];
                for ($i = 0; $i < 6; $i++) {
                    $date = now()->subMonths($i);
                    $attendances = \App\Models\Attendance::where('student_id', $student->id)
                        ->whereMonth('date', $date->month)
                        ->whereYear('date', $date->year)
                        ->get();
                        
                    $monthlyStats[] = [
                        'month' => $date->format('M Y'),
                        'hadir' => $attendances->where('status', 'hadir')->count(),
                        'total' => $attendances->count(),
                        'rate' => $attendances->count() > 0 
                            ? round(($attendances->where('status', 'hadir')->count() / $attendances->count()) * 100, 1) 
                            : 0
                    ];
                }
                
                return view('wali-kelas.students.show', compact('student', 'monthlyStats'));
            })->name('show');
        });
        
        // Monthly Reports for Wali Kelas
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
                
                $totalDays = $attendances->count();
                $hadirCount = $attendances->where('status', 'hadir')->count();
                
                $summary[] = [
                    'student' => $student,
                    'hadir' => $hadirCount,
                    'izin' => $attendances->where('status', 'izin')->count(),
                    'sakit' => $attendances->where('status', 'sakit')->count(),
                    'alpha' => $attendances->where('status', 'alpha')->count(),
                    'total_days' => $totalDays,
                    'attendance_rate' => $totalDays > 0 ? round(($hadirCount / $totalDays) * 100, 1) : 0
                ];
            }
            
            // Sort by attendance rate (lowest first for attention)
            usort($summary, function($a, $b) {
                return $a['attendance_rate'] <=> $b['attendance_rate'];
            });
            
            return view('wali-kelas.reports.monthly', compact('class', 'summary', 'month', 'year'));
        })->name('reports.monthly');
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

// API Routes untuk AJAX functionality
Route::middleware('auth')->prefix('api')->name('api.')->group(function () {
    // Quick stats for dashboard
    Route::get('/dashboard-stats', function () {
        $user = auth()->user();
        
        if ($user->isAdmin()) {
            return response()->json([
                'users' => \App\Models\User::count(),
                'classes' => \App\Models\Classes::count(),
                'students' => \App\Models\Student::where('status', 'active')->count(),
                'attendance_today' => \App\Models\Attendance::whereDate('date', today())->count(),
            ]);
        }
        
        if ($user->isWaliKelas()) {
            $class = $user->waliKelas;
            return response()->json([
                'total_students' => $class->students()->where('status', 'active')->count(),
                'attendance_today' => $class->attendances()->whereDate('date', today())->count(),
                'hadir_today' => $class->attendances()->whereDate('date', today())->where('status', 'hadir')->count(),
            ]);
        }
        
        return response()->json([]);
    })->name('dashboard.stats');
    
    // Get students for a class (for dynamic forms)
    Route::get('/classes/{class}/students', function (\App\Models\Classes $class) {
        return response()->json(
            $class->students()->where('status', 'active')
                  ->orderBy('name')
                  ->get(['id', 'name', 'nis'])
        );
    })->name('classes.students');
});

// Utility routes
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now(),
        'version' => '1.0.0',
        'environment' => app()->environment()
    ]);
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
        
        Route::get('/clear-cache', function () {
            \Artisan::call('cache:clear');
            \Artisan::call('config:clear');
            \Artisan::call('view:clear');
            \Artisan::call('route:clear');
            return 'All cache cleared successfully!';
        });
        
        Route::get('/test-attendance', function () {
            // Create sample attendance data for testing
            $classes = \App\Models\Classes::with('students')->get();
            
            foreach ($classes as $class) {
                foreach ($class->students as $student) {
                    for ($i = 1; $i <= 5; $i++) {
                        $date = now()->subDays($i);
                        if (!$date->isWeekend()) {
                            \App\Models\Attendance::updateOrCreate([
                                'student_id' => $student->id,
                                'date' => $date->format('Y-m-d')
                            ], [
                                'class_id' => $class->id,
                                'status' => collect(['hadir', 'hadir', 'hadir', 'izin', 'sakit'])->random(),
                                'recorded_by' => $class->wali_kelas_id ?? 1
                            ]);
                        }
                    }
                }
            }
            
            return 'Sample attendance data created!';
        });
    });
}