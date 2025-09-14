<?php
// app/Http/Controllers/GuruReportController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Classes;
use App\Models\Student;
use Carbon\Carbon;

class GuruReportController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:guru']);
    }

    public function index()
    {
        $classes = Classes::with(['waliKelas', 'students' => function($query) {
            $query->where('status', 'active');
        }])->orderBy('grade')->orderBy('name')->get();

        return view('guru.reports.index', compact('classes'));
    }

    public function dailyReport(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'class_id' => 'nullable|exists:classes,id'
        ]);

        $date = Carbon::parse($request->date);
        $classId = $request->class_id;

        // Build attendance query
        $attendancesQuery = Attendance::with(['student', 'class', 'recordedBy'])
            ->whereDate('date', $date);

        if ($classId && $classId !== 'all') {
            $attendancesQuery->where('class_id', $classId);
        }

        $attendances = $attendancesQuery->get();

        // Group by class for better display
        $attendancesByClass = $attendances->groupBy('class.name');

        // Calculate summary statistics
        $stats = [
            'total_recorded' => $attendances->count(),
            'hadir' => $attendances->where('status', 'hadir')->count(),
            'izin' => $attendances->where('status', 'izin')->count(),
            'sakit' => $attendances->where('status', 'sakit')->count(),
            'alpha' => $attendances->where('status', 'alpha')->count(),
        ];

        $stats['attendance_rate'] = $stats['total_recorded'] > 0 
            ? round(($stats['hadir'] / $stats['total_recorded']) * 100, 1)
            : 0;

        $classes = Classes::orderBy('grade')->orderBy('name')->get();

        return view('guru.reports.daily', compact(
            'attendancesByClass', 'stats', 'date', 'classId', 'classes'
        ));
    }

    public function monthlyReport(Request $request)
    {
        $request->validate([
            'month' => 'required|date_format:Y-m',
            'class_id' => 'nullable|exists:classes,id'
        ]);

        $monthYear = $request->month;
        $classId = $request->class_id;
        
        [$year, $month] = explode('-', $monthYear);

        // Get attendance data for the month
        $attendanceQuery = Attendance::with(['student', 'class'])
            ->whereMonth('date', $month)
            ->whereYear('date', $year);

        if ($classId && $classId !== 'all') {
            $attendanceQuery->where('class_id', $classId);
        }

        $attendances = $attendanceQuery->get();

        // Group by class and calculate statistics
        $summaryByClass = $attendances->groupBy('class.name')->map(function ($classAttendances) {
            $students = $classAttendances->groupBy('student_id');
            
            $classStats = [
                'class_name' => $classAttendances->first()->class->name,
                'total_students' => $students->count(),
                'total_records' => $classAttendances->count(),
                'hadir' => $classAttendances->where('status', 'hadir')->count(),
                'izin' => $classAttendances->where('status', 'izin')->count(),
                'sakit' => $classAttendances->where('status', 'sakit')->count(),
                'alpha' => $classAttendances->where('status', 'alpha')->count(),
            ];

            $classStats['attendance_rate'] = $classStats['total_records'] > 0 
                ? round(($classStats['hadir'] / $classStats['total_records']) * 100, 1)
                : 0;

            return $classStats;
        });

        // Overall monthly statistics
        $overallStats = [
            'total_classes' => $summaryByClass->count(),
            'total_records' => $attendances->count(),
            'hadir' => $attendances->where('status', 'hadir')->count(),
            'izin' => $attendances->where('status', 'izin')->count(),
            'sakit' => $attendances->where('status', 'sakit')->count(),
            'alpha' => $attendances->where('status', 'alpha')->count(),
        ];

        $overallStats['attendance_rate'] = $overallStats['total_records'] > 0 
            ? round(($overallStats['hadir'] / $overallStats['total_records']) * 100, 1)
            : 0;

        $classes = Classes::orderBy('grade')->orderBy('name')->get();

        return view('guru.reports.monthly', compact(
            'summaryByClass', 'overallStats', 'monthYear', 'classId', 'classes', 'year', 'month'
        ));
    }

    public function classDetail(Request $request, Classes $class)
    {
        $period = $request->get('period', 'monthly');
        $date = $request->get('date', now()->format('Y-m'));

        if ($period === 'daily') {
            $selectedDate = Carbon::parse($request->get('date', today()));
            
            $attendances = Attendance::with('student')
                ->where('class_id', $class->id)
                ->whereDate('date', $selectedDate)
                ->orderBy('student.name')
                ->get();

            $stats = [
                'total_students' => $class->students()->where('status', 'active')->count(),
                'hadir' => $attendances->where('status', 'hadir')->count(),
                'izin' => $attendances->where('status', 'izin')->count(),
                'sakit' => $attendances->where('status', 'sakit')->count(),
                'alpha' => $attendances->where('status', 'alpha')->count(),
            ];

            return view('guru.reports.class-daily', compact(
                'class', 'attendances', 'stats', 'selectedDate'
            ));
        } else {
            // Monthly view
            [$year, $month] = explode('-', $date);
            
            $students = $class->students()
                ->where('status', 'active')
                ->orderBy('name')
                ->get();

            $studentSummary = [];
            foreach ($students as $student) {
                $attendances = Attendance::where('student_id', $student->id)
                    ->whereMonth('date', $month)
                    ->whereYear('date', $year)
                    ->get();

                $studentSummary[] = [
                    'student' => $student,
                    'hadir' => $attendances->where('status', 'hadir')->count(),
                    'izin' => $attendances->where('status', 'izin')->count(),
                    'sakit' => $attendances->where('status', 'sakit')->count(),
                    'alpha' => $attendances->where('status', 'alpha')->count(),
                    'total_days' => $attendances->count(),
                    'attendance_rate' => $attendances->count() > 0 
                        ? round(($attendances->where('status', 'hadir')->count() / $attendances->count()) * 100, 1)
                        : 0
                ];
            }

            return view('guru.reports.class-monthly', compact(
                'class', 'studentSummary', 'date', 'year', 'month'
            ));
        }
    }
}