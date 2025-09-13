<?php
// app/Http/Controllers/Admin/UserManagementController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Classes;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('waliKelas');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Filter by wali kelas status
        if ($request->filled('wali_kelas')) {
            $query->where('is_wali_kelas', $request->wali_kelas == '1');
        }

        $users = $query->latest()->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(StoreUserRequest $request)
    {
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'nip' => $request->nip,
            'phone' => $request->phone,
            'is_wali_kelas' => $request->boolean('is_wali_kelas'),
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil ditambahkan!');
    }

    public function show(User $user)
    {
        $user->load('waliKelas.students', 'attendanceRecords');
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'nip' => $request->nip,
            'phone' => $request->phone,
            'is_wali_kelas' => $request->boolean('is_wali_kelas'),
        ];

        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        $user->update($userData);

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil diperbarui!');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Anda tidak dapat menghapus akun sendiri!');
        }

        if ($user->waliKelas) {
            return redirect()->route('admin.users.index')
                ->with('error', 'User tidak dapat dihapus karena masih menjadi wali kelas!');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil dihapus!');
    }
}