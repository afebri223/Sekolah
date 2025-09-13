<?php
// app/Http/Controllers/Admin/AttendanceReportController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Classes;
use App\Models\Student;
use Carbon\Carbon;

class AttendanceReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Attendance::with(['student', 'class', 'recordedBy']);

        // Date range filter
        if ($request->filled('start_date')) {
            $query->whereDate('date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('date', '<=', $request->end_date);
        }

        // Class filter
        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Student search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        $attendances = $query->latest('date')->paginate(20);
        $classes = Classes::orderBy('name')->get();

        // Statistics
        $statsQuery = clone $query;
        $stats = [
            'total_records' => $statsQuery->count(),
            'hadir' => $statsQuery->where('status', 'hadir')->count(),
            'izin' => $statsQuery->where('status', 'izin')->count(),
            'sakit' => $statsQuery->where('status', 'sakit')->count(),
            'alpha' => $statsQuery->where('status', 'alpha')->count(),
        ];

        return view('admin.reports.attendance', compact('attendances', 'classes', 'stats'));
    }

    public function export(Request $request)
    {
        // Implementasi export bisa ditambahkan nanti
        return response()->json(['message' => 'Export feature coming soon']);
    }

    public function summary(Request $request)
    {
        $month = $request->get('month', date('m'));
        $year = $request->get('year', date('Y'));

        $classes = Classes::with(['students' => function($query) {
            $query->where('status', 'active');
        }])->get();

        $summary = [];
        
        foreach ($classes as $class) {
            $classData = [
                'class' => $class,
                'total_students' => $class->students->count(),
                'attendance_summary' => []
            ];

            foreach ($class->students as $student) {
                $attendances = Attendance::where('student_id', $student->id)
                    ->whereMonth('date', $month)
                    ->whereYear('date', $year)
                    ->get();

                $classData['attendance_summary'][] = [
                    'student' => $student,
                    'hadir' => $attendances->where('status', 'hadir')->count(),
                    'izin' => $attendances->where('status', 'izin')->count(),
                    'sakit' => $attendances->where('status', 'sakit')->count(),
                    'alpha' => $attendances->where('status', 'alpha')->count(),
                    'total_days' => $attendances->count(),
                    'attendance_rate' => $attendances->count() > 0 ? 
                        round(($attendances->where('status', 'hadir')->count() / $attendances->count()) * 100, 1) : 0
                ];
            }

            $summary[] = $classData;
        }

        return view('admin.reports.summary', compact('summary', 'month', 'year'));
    }

    public function dailyReport(Request $request)
    {
        $date = $request->get('date', today()->format('Y-m-d'));
        
        $classes = Classes::with(['students' => function($query) {
            $query->where('status', 'active');
        }])->get();

        $dailyData = [];
        
        foreach ($classes as $class) {
            $attendances = Attendance::where('class_id', $class->id)
                ->whereDate('date', $date)
                ->with('student')
                ->get()
                ->keyBy('student_id');

            $classStats = [
                'class' => $class,
                'total_students' => $class->students->count(),
                'attended' => $attendances->where('status', 'hadir')->count(),
                'absent' => $class->students->count() - $attendances->count(),
                'students' => []
            ];

            foreach ($class->students as $student) {
                $attendance = $attendances->get($student->id);
                $classStats['students'][] = [
                    'student' => $student,
                    'status' => $attendance ? $attendance->status : 'belum_absen',
                    'notes' => $attendance ? $attendance->notes : null
                ];
            }

            $dailyData[] = $classStats;
        }

        return view('admin.reports.daily', compact('dailyData', 'date'));
    }
}