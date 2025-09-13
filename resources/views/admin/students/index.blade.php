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
                <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#importModal">
                    <i class="fas fa-file-import"></i> Import Bulk
                </button>
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
                <label class="form-label">Filter Kelas</label>
                <select name="class_id" class="form-select">
                    <option value="">Semua Kelas</option>
                    @foreach(\App\Models\Classes::all() as $class)
                    <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                        {{ $class->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                    <option value="graduated" {{ request('status') == 'graduated' ? 'selected' : '' }}>Lulus</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Cari</label>
                <input type="text" name="search" class="form-control" 
                       placeholder="NIS atau nama siswa..." value="{{ request('search') }}">
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

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>NIS</th>
                        <th>Nama</th>
                        <th>L/P</th>
                        <th>Kelas</th>
                        <th>Orang Tua</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                    <tr>
                        <td>{{ ($students->currentPage() - 1) * $students->perPage() + $loop->iteration }}</td>
                        <td><strong>{{ $student->nis }}</strong></td>
                        <td>
                            <div>
                                <strong>{{ $student->name }}</strong>
                                <br><small class="text-muted">{{ $student->birth_place }}, {{ $student->birth_date->format('d/m/Y') }}</small>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-{{ $student->gender == 'L' ? 'primary' : 'info' }}">
                                {{ $student->gender }}
                            </span>
                        </td>
                        <td>
                            @if($student->class)
                                <strong>{{ $student->class->name }}</strong>
                                <br><small class="text-muted">{{ $student->class->grade }} {{ $student->class->major }}</small>
                            @else
                                <span class="text-muted">Tidak ada</span>
                            @endif
                        </td>
                        <td>
                            <div>
                                <strong>{{ $student->parent_name }}</strong>
                                <br><small class="text-muted">{{ $student->parent_phone }}</small>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-{{ $student->status == 'active' ? 'success' : ($student->status == 'graduated' ? 'info' : 'secondary') }}">
                                {{ ucfirst($student->status) }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.students.show', $student) }}" class="btn btn-outline-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.students.edit', $student) }}" class="btn btn-outline-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.students.destroy', $student) }}" 
                                      style="display: inline-block;" 
                                      onsubmit="return confirm('Yakin ingin menghapus siswa ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center">Tidak ada data siswa</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($students->hasPages())
        <div class="d-flex justify-content-center mt-3">
            {{ $students->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Import Siswa Bulk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.students.bulk-import') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Pilih Kelas</label>
                        <select name="class_id" class="form-select" required>
                            <option value="">Pilih Kelas</option>
                            @foreach(\App\Models\Classes::all() as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">File Excel/CSV</label>
                        <input type="file" name="import_file" class="form-control" 
                               accept=".csv,.xlsx,.xls" required>
                        <small class="text-muted">Format: CSV, XLSX, XLS</small>
                    </div>
                    <div class="alert alert-info">
                        <strong>Format file:</strong>
                        <p class="mb-1">Kolom yang diperlukan: NIS, Nama, Jenis Kelamin (L/P), Tanggal Lahir, Tempat Lahir, Alamat, Nama Orang Tua, Telepon Orang Tua</p>
                        <a href="#" class="btn btn-sm btn-outline-info">Download Template</a>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload"></i> Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection