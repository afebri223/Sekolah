<?php
// app/Http/Controllers/DashboardController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Classes;
use App\Models\Student;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function adminDashboard()
    {
        $stats = [
            'total_guru' => User::where('role', 'guru')->count(),
            'total_kepala_sekolah' => User::where('role', 'kepala_sekolah')->count(),
            'total_kelas' => Classes::count(),
            'total_siswa' => Student::where('status', 'active')->count(),
            'total_wali_kelas' => User::where('is_wali_kelas', true)->count(),
            'total_admin' => User::where('role', 'admin')->count(),
            'attendance_today' => Attendance::whereDate('date', today())->count(),
            'classes_without_wali' => Classes::whereNull('wali_kelas_id')->count(),
        ];

        // Recent activities
        $recentUsers = User::latest()->limit(5)->get();
        $recentStudents = Student::latest()->limit(5)->get();
        $recentAttendances = Attendance::with(['student', 'class'])
            ->whereDate('date', today())
            ->latest()
            ->limit(10)
            ->get();

        return view('dashboards.admin', compact('stats', 'recentUsers', 'recentStudents', 'recentAttendances'));
    }

    public function kepalaSekolahDashboard()
    {
        $stats = [
            'total_guru' => User::where('role', 'guru')->count(),
            'total_kelas' => Classes::count(),
            'total_siswa' => Student::where('status', 'active')->count(),
            'attendance_today' => Attendance::whereDate('date', today())->count(),
        ];

        return view('dashboards.kepala-sekolah', compact('stats'));
    }

    public function guruDashboard()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $stats = [];

        if ($user->isWaliKelas()) {
            $class = $user->waliKelas;
            $stats = [
                'kelas' => $class->name,
                'total_siswa' => $class->students()->where('status', 'active')->count(),
                'hadir_hari_ini' => $class->attendances()
                    ->whereDate('date', today())
                    ->where('status', 'hadir')->count(),
                'tidak_hadir_hari_ini' => $class->attendances()
                    ->whereDate('date', today())
                    ->whereIn('status', ['izin', 'sakit', 'alpha'])->count(),
            ];
        }

        return view('dashboards.guru', compact('stats'));
    }

    public function waliKelasDashboard()
    {
        $user = Auth::user();
        $class = $user->waliKelas;

        if (!$class) {
            abort(403, 'Anda bukan wali kelas.');
        }

        $students = $class->students()->where('status', 'active')->get();
        $todayAttendances = $class->attendances()->whereDate('date', today())->get();

        return view('dashboards.wali-kelas', compact('class', 'students', 'todayAttendances'));
    }
}
