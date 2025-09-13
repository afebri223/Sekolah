<?php
// app/Http/Controllers/AttendanceController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;

class AttendanceController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        if (!$user->isWaliKelas()) {
            abort(403, 'Hanya wali kelas yang dapat mengakses fitur ini.');
        }

        $class = $user->waliKelas;
        $students = $class->students()->where('status', 'active')->get();
        
        // Cek apakah sudah absen hari ini
        $todayAttendances = Attendance::where('class_id', $class->id)
            ->whereDate('date', today())
            ->get()
            ->keyBy('student_id');

        return view('attendance.index', compact('class', 'students', 'todayAttendances'));
    }

    public function store(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        if (!$user->isWaliKelas()) {
            abort(403, 'Hanya wali kelas yang dapat melakukan absensi.');
        }

        $request->validate([
            'attendances' => 'required|array',
            'attendances.*' => 'required|in:hadir,izin,sakit,alpha',
            'notes' => 'nullable|array',
            'notes.*' => 'nullable|string|max:255',
        ]);

        $class = $user->waliKelas;
        $date = $request->input('date', today());

        foreach ($request->attendances as $studentId => $status) {
            Attendance::updateOrCreate(
                [
                    'student_id' => $studentId,
                    'date' => $date,
                ],
                [
                    'class_id' => $class->id,
                    'status' => $status,
                    'notes' => $request->notes[$studentId] ?? null,
                    'recorded_by' => $user->id,
                ]
            );
        }

        return redirect()->back()->with('success', 'Absensi berhasil disimpan!');
    }

    public function history()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        if (!$user->isWaliKelas()) {
            abort(403, 'Hanya wali kelas yang dapat mengakses riwayat absensi.');
        }

        $class = $user->waliKelas;
        $attendances = Attendance::where('class_id', $class->id)
            ->with(['student'])
            ->orderBy('date', 'desc')
            ->paginate(50);

        return view('attendance.history', compact('class', 'attendances'));
    }
}