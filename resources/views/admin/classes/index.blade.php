<!-- resources/views/admin/classes/index.blade.php -->
@extends('layouts.app')

@section('title', 'Kelola Kelas')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2><i class="fas fa-door-open"></i> Kelola Kelas</h2>
                <p class="text-muted">Manajemen kelas dan wali kelas</p>
            </div>
            <div>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                <a href="{{ route('admin.classes.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Kelas
                </a>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Nama Kelas</th>
                        <th>Tingkat</th>
                        <th>Jurusan</th>
                        <th>Wali Kelas</th>
                        <th>Jumlah Siswa</th>
                        <th>Kapasitas</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($classes as $class)
                    <tr>
                        <td>{{ ($classes->currentPage() - 1) * $classes->perPage() + $loop->iteration }}</td>
                        <td>
                            <strong>{{ $class->name }}</strong>
                        </td>
                        <td>
                            <span class="badge bg-info">Kelas {{ $class->grade }}</span>
                        </td>
                        <td>{{ $class->major ?: '-' }}</td>
                        <td>
                            @if($class->waliKelas)
                                <div>
                                    <strong>{{ $class->waliKelas->name }}</strong>
                                    <br><small class="text-muted">{{ $class->waliKelas->nip }}</small>
                                </div>
                            @else
                                <span class="text-muted">Belum ada</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-primary">{{ $class->students->count() }}</span>
                        </td>
                        <td>{{ $class->capacity }}</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.classes.show', $class) }}" class="btn btn-outline-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.classes.edit', $class) }}" class="btn btn-outline-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.classes.destroy', $class) }}" 
                                      style="display: inline-block;" 
                                      onsubmit="return confirm('Yakin ingin menghapus kelas ini?')">
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
                        <td colspan="8" class="text-center">Tidak ada data kelas</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($classes->hasPages())
        <div class="d-flex justify-content-center mt-3">
            {{ $classes->links() }}
        </div>
        @endif
    </div>
</div>
@endsection