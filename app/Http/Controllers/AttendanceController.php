<?php
// app/Http/Controllers/AttendanceController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'wali_kelas']);
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $class = $user->waliKelas;
        
        if (!$class) {
            return redirect()->route('guru.dashboard')
                ->with('error', 'Anda bukan wali kelas atau belum ditugaskan ke kelas manapun.');
        }

        $selectedDate = $request->get('date', today()->format('Y-m-d'));
        $date = Carbon::parse($selectedDate);

        // Get all active students in class
        $students = $class->students()
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        // Get existing attendances for selected date
        $existingAttendances = Attendance::where('class_id', $class->id)
            ->whereDate('date', $date)
            ->get()
            ->keyBy('student_id');

        // Check if attendance already recorded for today
        $isAttendanceComplete = $existingAttendances->count() === $students->count();
        
        // Quick stats for the week
        $weekStart = $date->copy()->startOfWeek();
        $weekEnd = $date->copy()->endOfWeek();
        
        $weeklyStats = [
            'hadir' => Attendance::where('class_id', $class->id)
                ->whereBetween('date', [$weekStart, $weekEnd])
                ->where('status', 'hadir')
                ->count(),
            'total_possible' => $students->count() * 5 // 5 working days
        ];

        return view('wali-kelas.attendance.index', compact(
            'class', 'students', 'existingAttendances', 'selectedDate', 
            'isAttendanceComplete', 'weeklyStats'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date|before_or_equal:today',
            'attendances' => 'required|array',
            'attendances.*.student_id' => 'required|exists:students,id',
            'attendances.*.status' => 'required|in:hadir,izin,sakit,alpha',
            'attendances.*.notes' => 'nullable|string|max:500'
        ]);

        $user = Auth::user();
        $class = $user->waliKelas;
        $date = Carbon::parse($request->date);

        if (!$class) {
            return back()->with('error', 'Anda bukan wali kelas.');
        }

        // Validate all students belong to this class
        $studentIds = collect($request->attendances)->pluck('student_id');
        $validStudents = Student::where('class_id', $class->id)
            ->whereIn('id', $studentIds)
            ->count();

        if ($validStudents !== $studentIds->count()) {
            return back()->with('error', 'Ada siswa yang tidak valid dalam daftar absensi.');
        }

        try {
            DB::beginTransaction();

            // Delete existing attendances for this date and class
            Attendance::where('class_id', $class->id)
                ->whereDate('date', $date)
                ->delete();

            // Insert new attendances
            $attendanceData = [];
            foreach ($request->attendances as $attendance) {
                $attendanceData[] = [
                    'student_id' => $attendance['student_id'],
                    'class_id' => $class->id,
                    'date' => $date->format('Y-m-d'),
                    'status' => $attendance['status'],
                    'notes' => $attendance['notes'] ?? null,
                    'recorded_by' => $user->id,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }

            Attendance::insert($attendanceData);

            DB::commit();

            return redirect()->route('wali-kelas.attendance.index', ['date' => $date->format('Y-m-d')])
                ->with('success', 'Absensi berhasil disimpan untuk tanggal ' . $date->format('d M Y'));

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan saat menyimpan absensi: ' . $e->getMessage());
        }
    }

    public function history(Request $request)
    {
        $user = Auth::user();
        $class = $user->waliKelas;

        if (!$class) {
            return redirect()->route('guru.dashboard')
                ->with('error', 'Anda bukan wali kelas.');
        }

        // Filter parameters
        $month = $request->get('month', now()->format('Y-m'));
        $studentId = $request->get('student_id');

        [$year, $monthNum] = explode('-', $month);

        // Get attendances for the month
        $attendancesQuery = Attendance::with(['student'])
            ->where('class_id', $class->id)
            ->whereMonth('date', $monthNum)
            ->whereYear('date', $year);

        if ($studentId) {
            $attendancesQuery->where('student_id', $studentId);
        }

        $attendances = $attendancesQuery->orderBy('date', 'desc')
            ->paginate(20)
            ->appends($request->query());

        // Get students for filter dropdown
        $students = $class->students()
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        // Monthly summary
        $monthlySummary = Attendance::where('class_id', $class->id)
            ->whereMonth('date', $monthNum)
            ->whereYear('date', $year)
            ->selectRaw('
                status,
                COUNT(*) as count,
                COUNT(DISTINCT student_id) as unique_students,
                COUNT(DISTINCT date) as unique_dates
            ')
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        return view('wali-kelas.attendance.history', compact(
            'class', 'attendances', 'students', 'month', 'studentId', 'monthlySummary'
        ));
    }

    public function quickMark(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'status' => 'required|in:hadir,izin,sakit,alpha',
            'date' => 'required|date',
            'notes' => 'nullable|string|max:500'
        ]);

        $user = Auth::user();
        $class = $user->waliKelas;
        $student = Student::findOrFail($request->student_id);

        // Verify student belongs to this class
        if ($student->class_id !== $class->id) {
            return response()->json(['error' => 'Siswa tidak ada di kelas Anda'], 403);
        }

        try {
            Attendance::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'date' => $request->date
                ],
                [
                    'class_id' => $class->id,
                    'status' => $request->status,
                    'notes' => $request->notes,
                    'recorded_by' => $user->id
                ]
            );

            return response()->json([
                'success' => true,
                'message' => "Absensi {$student->name} berhasil diperbarui"
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal menyimpan absensi'], 500);
        }
    }
}