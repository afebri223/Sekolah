<!-- resources/views/admin/users/index.blade.php -->
@extends('layouts.app')

@section('title', 'Kelola Pengguna')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2><i class="fas fa-users"></i> Kelola Pengguna</h2>
                <p class="text-muted">Manajemen admin, kepala sekolah, dan guru</p>
            </div>
            <div>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah User
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Filter Role</label>
                <select name="role" class="form-select">
                    <option value="">Semua Role</option>
                    <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Administrator</option>
                    <option value="kepala_sekolah" {{ request('role') == 'kepala_sekolah' ? 'selected' : '' }}>Kepala Sekolah</option>
                    <option value="guru" {{ request('role') == 'guru' ? 'selected' : '' }}>Guru</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Status Wali Kelas</label>
                <select name="wali_kelas" class="form-select">
                    <option value="">Semua</option>
                    <option value="1" {{ request('wali_kelas') == '1' ? 'selected' : '' }}>Ya</option>
                    <option value="0" {{ request('wali_kelas') == '0' ? 'selected' : '' }}>Tidak</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Cari</label>
                <input type="text" name="search" class="form-control" 
                       placeholder="Nama atau email..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <div class="d-grid">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="fas fa-search"></i> Filter
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Users Table -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>NIP</th>
                        <th>Wali Kelas</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td>{{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}</td>
                        <td>
                            <strong>{{ $user->name }}</strong>
                            @if($user->phone)
                                <br><small class="text-muted">{{ $user->phone }}</small>
                            @endif
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <span class="badge bg-{{ $user->role == 'admin' ? 'danger' : ($user->role == 'kepala_sekolah' ? 'success' : 'primary') }}">
                                {{ $user->getRoleName() }}
                            </span>
                        </td>
                        <td>{{ $user->nip ?: '-' }}</td>
                        <td>
                            @if($user->is_wali_kelas)
                                <span class="badge bg-success">
                                    Ya - {{ $user->waliKelas->name ?? 'Belum Ada Kelas' }}
                                </span>
                            @else
                                <span class="badge bg-secondary">Tidak</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-success">Aktif</span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.users.show', $user) }}" class="btn btn-outline-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-outline-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if($user->id !== auth()->id())
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" 
                                      style="display: inline-block;" 
                                      onsubmit="return confirm('Yakin ingin menghapus user ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center">Tidak ada data user</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($users->hasPages())
        <div class="d-flex justify-content-center mt-3">
            {{ $users->links() }}
        </div>
        @endif
    </div>
</div>
@endsection