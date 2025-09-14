<?php
// app/Http/Controllers/KepalaSekolahReportController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Classes;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class KepalaSekolahReportController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:kepala_sekolah']);
    }

    public function index(Request $request)
    {
        $classes = Classes::with('waliKelas')->orderBy('grade')->orderBy('name')->get();
        
        // Default filter values
        $selectedClass = $request->get('class_id', 'all');
        $selectedPeriod = $request->get('period', 'monthly');
        $selectedDate = $request->get('date', now()->format('Y-m'));
        
        return view('kepala-sekolah.reports.index', compact(
            'classes', 'selectedClass', 'selectedPeriod', 'selectedDate'
        ));
    }

    public function dailyReport(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'class_id' => 'nullable|exists:classes,id'
        ]);

        $date = Carbon::parse($request->date);
        $classId = $request->class_id;

        $query = Attendance::with(['student.class', 'recordedBy'])
            ->whereDate('date', $date);

        if ($classId && $classId !== 'all') {
            $query->where('class_id', $classId);
        }

        $attendances = $query->get()->groupBy('class.name');
        
        // Summary stats
        $stats = [
            'total_students' => Student::when($classId && $classId !== 'all', 
                fn($q) => $q->where('class_id', $classId)
            )->where('status', 'active')->count(),
            
            'total_hadir' => $query->clone()->where('status', 'hadir')->count(),
            'total_izin' => $query->clone()->where('status', 'izin')->count(),
            'total_sakit' => $query->clone()->where('status', 'sakit')->count(),
            'total_alpha' => $query->clone()->where('status', 'alpha')->count(),
        ];

        $classes = Classes::orderBy('grade')->orderBy('name')->get();
        
        return view('kepala-sekolah.reports.daily', compact(
            'attendances', 'stats', 'date', 'classId', 'classes'
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

        // Get all students
        $studentsQuery = Student::with('class')
            ->where('status', 'active');
            
        if ($classId && $classId !== 'all') {
            $studentsQuery->where('class_id', $classId);
        }
        
        $students = $studentsQuery->get();

        // Calculate attendance summary for each student
        $summary = [];
        foreach ($students as $student) {
            $attendances = Attendance::where('student_id', $student->id)
                ->whereMonth('date', $month)
                ->whereYear('date', $year)
                ->get();

            $summary[] = [
                'student' => $student,
                'class' => $student->class,
                'hadir' => $attendances->where('status', 'hadir')->count(),
                'izin' => $attendances->where('status', 'izin')->count(),
                'sakit' => $attendances->where('status', 'sakit')->count(),
                'alpha' => $attendances->where('status', 'alpha')->count(),
                'total_days' => $attendances->count(),
                'percentage' => $attendances->count() > 0 
                    ? round(($attendances->where('status', 'hadir')->count() / $attendances->count()) * 100, 1)
                    : 0
            ];
        }

        // Group by class
        $summaryByClass = collect($summary)->groupBy('class.name');
        
        $classes = Classes::orderBy('grade')->orderBy('name')->get();

        return view('kepala-sekolah.reports.monthly', compact(
            'summaryByClass', 'monthYear', 'classId', 'classes', 'year', 'month'
        ));
    }

    public function yearlyReport(Request $request)
    {
        $request->validate([
            'year' => 'required|integer|min:2020|max:2030',
            'class_id' => 'nullable|exists:classes,id'
        ]);

        $year = $request->year;
        $classId = $request->class_id;

        // Monthly breakdown for the year
        $monthlyStats = [];
        
        for ($month = 1; $month <= 12; $month++) {
            $attendanceQuery = Attendance::whereMonth('date', $month)
                ->whereYear('date', $year);
                
            if ($classId && $classId !== 'all') {
                $attendanceQuery->where('class_id', $classId);
            }

            $monthlyStats[] = [
                'month' => $month,
                'month_name' => Carbon::create($year, $month, 1)->format('F'),
                'hadir' => $attendanceQuery->clone()->where('status', 'hadir')->count(),
                'izin' => $attendanceQuery->clone()->where('status', 'izin')->count(),
                'sakit' => $attendanceQuery->clone()->where('status', 'sakit')->count(),
                'alpha' => $attendanceQuery->clone()->where('status', 'alpha')->count(),
                'total' => $attendanceQuery->count(),
            ];
        }

        // Class performance ranking
        $classRanking = [];
        if ($classId === 'all' || !$classId) {
            $classes = Classes::with('students')->get();
            
            foreach ($classes as $class) {
                $totalAttendances = Attendance::where('class_id', $class->id)
                    ->whereYear('date', $year)
                    ->count();
                    
                $hadirCount = Attendance::where('class_id', $class->id)
                    ->whereYear('date', $year)
                    ->where('status', 'hadir')
                    ->count();

                $classRanking[] = [
                    'class' => $class,
                    'total_students' => $class->students()->where('status', 'active')->count(),
                    'attendance_rate' => $totalAttendances > 0 
                        ? round(($hadirCount / $totalAttendances) * 100, 1)
                        : 0,
                    'total_attendances' => $totalAttendances,
                    'hadir_count' => $hadirCount
                ];
            }

            // Sort by attendance rate
            $classRanking = collect($classRanking)
                ->sortByDesc('attendance_rate')
                ->values()
                ->all();
        }

        $classes = Classes::orderBy('grade')->orderBy('name')->get();

        return view('kepala-sekolah.reports.yearly', compact(
            'monthlyStats', 'classRanking', 'year', 'classId', 'classes'
        ));
    }

    public function exportReport(Request $request)
    {
        $request->validate([
            'type' => 'required|in:daily,monthly,yearly',
            'format' => 'required|in:pdf,excel'
        ]);

        // Implementation would depend on your export requirements
        // This is a placeholder for export functionality
        
        return back()->with('info', 'Fitur export sedang dalam pengembangan.');
    }
}