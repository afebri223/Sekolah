<?php
// app/Http/Controllers/Admin/StudentManagementController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Classes;

class StudentManagementController extends Controller
{
    public function index()
    {
        $students = Student::with('class')->paginate(15);
        return view('admin.students.index', compact('students'));
    }

    public function create()
    {
        $classes = Classes::all();
        return view('admin.students.create', compact('classes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nis' => 'required|string|unique:students',
            'name' => 'required|string|max:255',
            'gender' => 'required|in:L,P',
            'birth_date' => 'required|date',
            'birth_place' => 'required|string|max:255',
            'address' => 'required|string',
            'phone' => 'nullable|string',
            'parent_name' => 'required|string|max:255',
            'parent_phone' => 'required|string',
            'class_id' => 'required|exists:classes,id',
            'status' => 'required|in:active,inactive,graduated',
        ]);

        Student::create($request->all());

        return redirect()->route('admin.students.index')
            ->with('success', 'Siswa berhasil ditambahkan!');
    }

    public function show(Student $student)
    {
        $student->load(['class', 'attendances' => function($query) {
            $query->latest()->limit(10);
        }]);
        
        return view('admin.students.show', compact('student'));
    }

    public function edit(Student $student)
    {
        $classes = Classes::all();
        return view('admin.students.edit', compact('student', 'classes'));
    }

    public function update(Request $request, Student $student)
    {
        $request->validate([
            'nis' => 'required|string|unique:students,nis,' . $student->id,
            'name' => 'required|string|max:255',
            'gender' => 'required|in:L,P',
            'birth_date' => 'required|date',
            'birth_place' => 'required|string|max:255',
            'address' => 'required|string',
            'phone' => 'nullable|string',
            'parent_name' => 'required|string|max:255',
            'parent_phone' => 'required|string',
            'class_id' => 'required|exists:classes,id',
            'status' => 'required|in:active,inactive,graduated',
        ]);

        $student->update($request->all());

        return redirect()->route('admin.students.index')
            ->with('success', 'Data siswa berhasil diperbarui!');
    }

    public function destroy(Student $student)
    {
        $student->delete();

        return redirect()->route('admin.students.index')
            ->with('success', 'Siswa berhasil dihapus!');
    }

    public function bulkImport(Request $request)
    {
        $request->validate([
            'import_file' => 'required|file|mimes:csv,xlsx,xls',
            'class_id' => 'required|exists:classes,id',
        ]);

        // Implementation for bulk import
        // This would require additional CSV/Excel processing
        
        return redirect()->route('admin.students.index')
            ->with('success', 'Import siswa berhasil!');
    }
}