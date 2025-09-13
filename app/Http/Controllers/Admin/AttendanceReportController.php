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

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('date', '<=', $request->end_date);
        }

        // Filter by class
        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $attendances = $query->latest('date')->paginate(20);
        $classes = Classes::all();

        // Statistics
        $stats = [
            'total_records' => $query->count(),
            'hadir' => $query->clone()->where('status', 'hadir')->count(),
            'izin' => $query->clone()->where('status', 'izin')->count(),
            'sakit' => $query->clone()->where('status', 'sakit')->count(),
            'alpha' => $query->clone()->where('status', 'alpha')->count(),
        ];

        return view('admin.reports.attendance', compact('attendances', 'classes', 'stats'));
    }

    public function export(Request $request)
    {
        // Implementation for exporting attendance data to Excel/PDF
        // This would require additional export libraries
        
        return response()->download($pathToFile);
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
                ];
            }

            $summary[] = $classData;
        }

        return view('admin.reports.summary', compact('summary', 'month', 'year'));
    }
}