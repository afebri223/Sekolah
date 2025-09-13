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

<!-- Advanced Filter Section -->
<div class="card mb-4">
    <div class="card-header">
        <h6 class="mb-0"><i class="fas fa-filter"></i> Filter & Pencarian</h6>
    </div>
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
            <div class="col-md-2">
                <label class="form-label">Wali Kelas</label>
                <select name="wali_kelas" class="form-select">
                    <option value="">Semua</option>
                    <option value="1" {{ request('wali_kelas') == '1' ? 'selected' : '' }}>Ya</option>
                    <option value="0" {{ request('wali_kelas') == '0' ? 'selected' : '' }}>Tidak</option>
                </select>
            </div>
            <div class="col-md-5">
                <label class="form-label">Cari User</label>
                <input type="text" name="search" class="form-control" 
                       placeholder="Nama, email, atau NIP..." value="{{ request('search') }}">
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

        @if(request()->hasAny(['role', 'wali_kelas', 'search']))
        <div class="mt-3">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <small class="text-muted">Filter aktif:</small>
                @if(request('role'))
                    <span class="badge bg-primary">
                        Role: {{ ucfirst(str_replace('_', ' ', request('role'))) }}
                        <a href="{{ request()->url() }}?{{ http_build_query(request()->except('role')) }}" class="text-white ms-1">×</a>
                    </span>
                @endif
                @if(request('wali_kelas'))
                    <span class="badge bg-success">
                        Wali Kelas: {{ request('wali_kelas') == '1' ? 'Ya' : 'Tidak' }}
                        <a href="{{ request()->url() }}?{{ http_build_query(request()->except('wali_kelas')) }}" class="text-white ms-1">×</a>
                    </span>
                @endif
                @if(request('search'))
                    <span class="badge bg-info">
                        Pencarian: "{{ request('search') }}"
                        <a href="{{ request()->url() }}?{{ http_build_query(request()->except('search')) }}" class="text-white ms-1">×</a>
                    </span>
                @endif
                <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-times"></i> Reset
                </a>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Results Summary -->
@if($users->total() > 0)
<div class="alert alert-info">
    <i class="fas fa-info-circle"></i> 
    Menampilkan {{ $users->count() }} dari {{ $users->total() }} user
    @if(request()->hasAny(['role', 'wali_kelas', 'search']))
        yang memenuhi kriteria filter
    @endif
</div>
@endif

<!-- Users Table -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Foto</th>
                        <th>Nama & Email</th>
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
                            <div class="avatar-circle bg-{{ $user->role == 'admin' ? 'danger' : ($user->role == 'kepala_sekolah' ? 'success' : 'primary') }}">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        </td>
                        <td>
                            <div>
                                <strong>{{ $user->name }}</strong>
                                <br><small class="text-muted">{{ $user->email }}</small>
                                @if($user->phone)
                                    <br><small class="text-muted"><i class="fas fa-phone"></i> {{ $user->phone }}</small>
                                @endif
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-{{ $user->role == 'admin' ? 'danger' : ($user->role == 'kepala_sekolah' ? 'success' : 'primary') }}">
                                {{ $user->getRoleName() }}
                            </span>
                        </td>
                        <td>{{ $user->nip ?: '-' }}</td>
                        <td>
                            @if($user->is_wali_kelas)
                                <span class="badge bg-success">
                                    <i class="fas fa-check"></i> Ya
                                </span>
                                @if($user->waliKelas)
                                    <br><small class="text-muted">Kelas {{ $user->waliKelas->name }}</small>
                                @else
                                    <br><small class="text-warning">Belum Ada Kelas</small>
                                @endif
                            @else
                                <span class="badge bg-secondary">
                                    <i class="fas fa-times"></i> Tidak
                                </span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-success">
                                <i class="fas fa-check-circle"></i> Aktif
                            </span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.users.show', $user) }}" class="btn btn-outline-info" title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-outline-warning" title="Edit User">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if($user->id !== auth()->id())
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" 
                                      style="display: inline-block;" 
                                      onsubmit="return confirm('Yakin ingin menghapus user ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Hapus User">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <div>
                                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                <h5>Tidak ada data user</h5>
                                @if(request()->hasAny(['role', 'wali_kelas', 'search']))
                                    <p class="text-muted">Coba ubah filter atau kriteria pencarian</p>
                                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-primary">Reset Filter</a>
                                @else
                                    <p class="text-muted">Belum ada user yang terdaftar</p>
                                    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">Tambah User Pertama</a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($users->hasPages())
        <div class="d-flex justify-content-center mt-3">
            {{ $users->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

@section('styles')
<style>
.avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    color: white;
    font-size: 16px;
}
</style>
@endsection