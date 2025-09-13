<?php
// app/Http/Controllers/Admin/ClassManagementController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Classes;
use App\Models\User;
use App\Http\Requests\StoreClassRequest;
use App\Http\Requests\UpdateClassRequest;

class ClassManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = Classes::with(['waliKelas', 'students']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
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

        // Filter by wali kelas status
        if ($request->filled('has_wali_kelas')) {
            if ($request->has_wali_kelas == '1') {
                $query->whereNotNull('wali_kelas_id');
            } else {
                $query->whereNull('wali_kelas_id');
            }
        }

        $classes = $query->latest()->paginate(15);

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

    public function store(StoreClassRequest $request)
    {
        Classes::create($request->validated());

        return redirect()->route('admin.classes.index')
            ->with('success', 'Kelas berhasil ditambahkan!');
    }

    public function show(Classes $class)
    {
        $class->load(['waliKelas', 'students' => function($query) {
            $query->where('status', 'active');
        }]);
        
        return view('admin.classes.show', compact('class'));
    }

    public function edit(Classes $class)
    {
        $waliKelasOptions = User::where('role', 'guru')
            ->where('is_wali_kelas', true)
            ->where(function($query) use ($class) {
                $query->whereDoesntHave('waliKelas')
                      ->orWhere('id', $class->wali_kelas_id);
            })
            ->get();
        
        return view('admin.classes.edit', compact('class', 'waliKelasOptions'));
    }

    public function update(UpdateClassRequest $request, Classes $class)
    {
        $class->update($request->validated());

        return redirect()->route('admin.classes.index')
            ->with('success', 'Kelas berhasil diperbarui!');
    }

    public function destroy(Classes $class)
    {
        if ($class->students()->where('status', 'active')->count() > 0) {
            return redirect()->route('admin.classes.index')
                ->with('error', 'Kelas tidak dapat dihapus karena masih memiliki siswa aktif!');
        }

        $class->delete();

        return redirect()->route('admin.classes.index')
            ->with('success', 'Kelas berhasil dihapus!');
    }
}