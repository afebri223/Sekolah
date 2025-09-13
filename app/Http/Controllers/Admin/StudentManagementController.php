<?php
// app/Http/Controllers/Admin/StudentManagementController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Classes;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;

class StudentManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::with('class');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nis', 'like', "%{$search}%")
                    ->orWhere('parent_name', 'like', "%{$search}%");
            });
        }

        // Filter by class
        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by gender
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        $students = $query->latest()->paginate(15);

        // Get all classes for filter dropdown
        $classes = Classes::orderBy('grade')->orderBy('name')->get();

        return view('admin.students.index', compact('students', 'classes'));
    }

    public function create()
    {
        $classes = Classes::orderBy('grade')->orderBy('name')->get();
        return view('admin.students.create', compact('classes'));
    }

    public function store(StoreStudentRequest $request)
    {
        // Check class capacity
        $class = Classes::find($request->class_id);
        $currentStudents = $class->students()->where('status', 'active')->count();

        if ($currentStudents >= $class->capacity) {
            return back()->withErrors(['class_id' => 'Kelas sudah penuh! Kapasitas: ' . $class->capacity])
                ->withInput();
        }

        Student::create($request->validated());

        return redirect()->route('admin.students.index')
            ->with('success', 'Siswa berhasil ditambahkan!');
    }

    public function show(Student $student)
    {
        $student->load(['class.waliKelas', 'attendances' => function ($query) {
            $query->with('recordedBy')->latest()->limit(20);
        }]);

        return view('admin.students.show', compact('student'));
    }

    public function edit(Student $student)
    {
        $classes = Classes::orderBy('grade')->orderBy('name')->get();
        return view('admin.students.edit', compact('student', 'classes'));
    }

    public function update(UpdateStudentRequest $request, Student $student)
    {
        // Check if changing class and validate capacity
        if ($request->class_id != $student->class_id) {
            $newClass = Classes::find($request->class_id);
            $currentStudents = $newClass->students()->where('status', 'active')->count();

            if ($currentStudents >= $newClass->capacity) {
                return back()->withErrors(['class_id' => 'Kelas tujuan sudah penuh! Kapasitas: ' . $newClass->capacity])
                    ->withInput();
            }
        }

        $student->update($request->validated());

        return redirect()->route('admin.students.index')
            ->with('success', 'Data siswa berhasil diperbarui!');
    }

    public function destroy(Student $student)
    {
        // Check if student has attendance records
        if ($student->attendances()->count() > 0) {
            return redirect()->route('admin.students.index')
                ->with('error', 'Siswa tidak dapat dihapus karena memiliki data absensi! Ubah status menjadi "Tidak Aktif" jika diperlukan.');
        }

        $student->delete();

        return redirect()->route('admin.students.index')
            ->with('success', 'Siswa berhasil dihapus!');
    }

    public function bulkImport(Request $request)
    {
        $request->validate([
            'import_file' => 'required|file|mimes:csv,xlsx,xls|max:2048',
            'class_id' => 'required|exists:classes,id',
        ]);

        // Note: Untuk implementasi penuh, gunakan library seperti Laravel Excel
        // Ini hanya placeholder untuk struktur

        return redirect()->route('admin.students.index')
            ->with('info', 'Fitur import bulk sedang dalam pengembangan. Silakan tambah siswa secara manual.');
    }

    // Additional helper methods
    public function getClassStudents(Classes $class)
    {
        return $class->students()->where('status', 'active')->get();
    }

    public function transferStudent(Request $request, Student $student)
    {
        $request->validate([
            'new_class_id' => 'required|exists:classes,id|different:' . $student->class_id,
        ]);

        $newClass = Classes::find($request->new_class_id);
        $currentStudents = $newClass->students()->where('status', 'active')->count();

        if ($currentStudents >= $newClass->capacity) {
            return back()->withErrors(['new_class_id' => 'Kelas tujuan sudah penuh!']);
        }

        $oldClass = $student->class->name;
        $student->update(['class_id' => $request->new_class_id]);

        return redirect()->route('admin.students.show', $student)
            ->with('success', "Siswa berhasil dipindah dari kelas {$oldClass} ke kelas {$newClass->name}!");
    }
}
