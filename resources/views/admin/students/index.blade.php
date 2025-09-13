<!-- resources/views/admin/students/index.blade.php -->
@extends('layouts.app')

@section('title', 'Kelola Siswa')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2><i class="fas fa-user-graduate"></i> Kelola Siswa</h2>
                <p class="text-muted">Manajemen data siswa</p>
            </div>
            <div>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                <a href="{{ route('admin.students.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Siswa
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
                <label class="form-label">Cari</label>
                <input type="text" name="search" class="form-control" 
                       placeholder="Nama, NIS, Nama Orang Tua..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Kelas</label>
                <select name="class_id" class="form-select">
                    <option value="">Semua Kelas</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                            {{ $class->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Jenis Kelamin</label>
                <select name="gender" class="form-select">
                    <option value="">Semua</option>
                    <option value="L" {{ request('gender') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="P" {{ request('gender') == 'P' ? 'selected' : '' }}>Perempuan</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                    <option value="graduated" {{ request('status') == 'graduated' ? 'selected' : '' }}>Lulus</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">&nbsp;</label>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Cari
                    </button>
                    <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i> Reset
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Students Table -->
<div class="card">
    <div class="card-body">
        @if($students->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>NIS</th>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th>Jenis Kelamin</th>
                            <th>Tempat Lahir</th>
                            <th>Orang Tua</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $student)
                            <tr>
                                <td><strong>{{ $student->nis }}</strong></td>
                                <td>
                                    <div>
                                        <strong>{{ $student->name }}</strong>
                                        @if($student->phone)
                                            <br><small class="text-muted">
                                                <i class="fas fa-phone"></i> {{ $student->phone }}
                                            </small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $student->class->name }}</span>
                                    <br><small class="text-muted">{{ $student->class->major }}</small>
                                </td>
                                <td>
                                    @if($student->gender == 'L')
                                        <span class="badge bg-primary">Laki-laki</span>
                                    @else
                                        <span class="badge bg-danger">Perempuan</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $student->birth_place }}
                                    <br><small class="text-muted">{{ \Carbon\Carbon::parse($student->birth_date)->format('d M Y') }}</small>
                                </td>
                                <td>
                                    <div>
                                        <strong>{{ $student->parent_name }}</strong>
                                        <br><small class="text-muted">
                                            <i class="fas fa-phone"></i> {{ $student->parent_phone }}
                                        </small>
                                    </div>
                                </td>
                                <td>
                                    @switch($student->status)
                                        @case('active')
                                            <span class="badge bg-success">Aktif</span>
                                            @break
                                        @case('inactive')
                                            <span class="badge bg-warning">Tidak Aktif</span>
                                            @break
                                        @case('graduated')
                                            <span class="badge bg-info">Lulus</span>
                                            @break
                                    @endswitch
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.students.show', $student) }}" 
                                           class="btn btn-outline-info" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.students.edit', $student) }}" 
                                           class="btn btn-outline-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-outline-danger" 
                                                title="Hapus" data-bs-toggle="modal" 
                                                data-bs-target="#deleteModal{{ $student->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Delete Modal -->
                            <div class="modal fade" id="deleteModal{{ $student->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Konfirmasi Hapus</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Apakah Anda yakin ingin menghapus siswa <strong>{{ $student->name }}</strong>?</p>
                                            <p class="text-danger"><small>Tindakan ini tidak dapat dibatalkan!</small></p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <form method="POST" action="{{ route('admin.students.destroy', $student) }}" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger">Hapus</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    <small class="text-muted">
                        Menampilkan {{ $students->firstItem() }} sampai {{ $students->lastItem() }} 
                        dari {{ $students->total() }} siswa
                    </small>
                </div>
                <div>
                    {{ $students->links() }}
                </div>
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-user-graduate fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Belum ada siswa</h5>
                <p class="text-muted">Mulai dengan menambahkan siswa baru</p>
                <a href="{{ route('admin.students.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Siswa Pertama
                </a>
            </div>
        @endif
    </div>
</div>
@endsection