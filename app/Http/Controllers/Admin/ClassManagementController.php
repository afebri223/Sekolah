<?php
// app/Http/Controllers/Admin/ClassManagementController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Classes;
use App\Models\User;

class ClassManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = Classes::with(['waliKelas', 'students']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('grade', 'like', "%{$search}%")
                    ->orWhere('major', 'like', "%{$search}%");
            });
        }

        // Filter by grade
        if ($request->filled('grade')) {
            $query->where('grade', $request->grade);
        }

        // Filter by major
        if ($request->filled('major')) {
            $query->where('major', $request->major);
        }

        $classes = $query->paginate(15)->appends($request->query());
        return view('admin.classes.index', compact('classes'));
    }

    public function create()
    {
        $waliKelasOptions = User::where('role', 'guru')
            ->where('is_wali_kelas', true)
            ->whereDoesntHave('waliKelas')
            ->get();

        return view('admin.classes.create', compact('waliKelasOptions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:classes',
            'grade' => 'required|string|max:10',
            'major' => 'nullable|string|max:50',
            'wali_kelas_id' => 'nullable|exists:users,id',
            'capacity' => 'required|integer|min:1|max:50',
        ]);

        Classes::create($request->all());

        return redirect()->route('admin.classes.index')
            ->with('success', 'Kelas berhasil ditambahkan!');
    }

    public function show(Classes $class)
    {
        $class->load(['waliKelas', 'students' => function ($query) {
            $query->where('status', 'active');
        }]);

        return view('admin.classes.show', compact('class'));
    }

    public function edit(Classes $class)
    {
        $waliKelasOptions = User::where('role', 'guru')
            ->where('is_wali_kelas', true)
            ->where(function ($query) use ($class) {
                $query->whereDoesntHave('waliKelas')
                    ->orWhere('id', $class->wali_kelas_id);
            })
            ->get();

        return view('admin.classes.edit', compact('class', 'waliKelasOptions'));
    }

    public function update(Request $request, Classes $class)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:classes,name,' . $class->id,
            'grade' => 'required|string|max:10',
            'major' => 'nullable|string|max:50',
            'wali_kelas_id' => 'nullable|exists:users,id',
            'capacity' => 'required|integer|min:1|max:50',
        ]);

        $class->update($request->all());

        return redirect()->route('admin.classes.index')
            ->with('success', 'Kelas berhasil diperbarui!');
    }

    public function destroy(Classes $class)
    {
        // Check if class has active students
        if ($class->students()->where('status', 'active')->count() > 0) {
            return redirect()->route('admin.classes.index')
                ->with('error', 'Kelas tidak dapat dihapus karena masih memiliki siswa aktif!');
        }

        $class->delete();

        return redirect()->route('admin.classes.index')
            ->with('success', 'Kelas berhasil dihapus!');
    }
}
