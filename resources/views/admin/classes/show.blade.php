<!-- resources/views/admin/classes/show.blade.php -->
@extends('layouts.app')

@section('title', 'Detail Kelas')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2><i class="fas fa-door-open"></i> Detail Kelas {{ $class->name }}</h2>
                <p class="text-muted">Informasi lengkap kelas dan siswa</p>
            </div>
            <div>
                <a href="{{ route('admin.classes.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                <a href="{{ route('admin.classes.edit', $class) }}" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <form method="POST" action="{{ route('admin.classes.destroy', $class) }}" 
                      style="display: inline-block;" 
                      onsubmit="return confirm('Yakin ingin menghapus kelas ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-info-circle"></i> Informasi Kelas</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <td width="30%"><strong>Nama Kelas:</strong></td>
                        <td>{{ $class->name }}</td>
                    </tr>
                    <tr>
                        <td><strong>Tingkat:</strong></td>
                        <td>
                            <span class="badge bg-info">Kelas {{ $class->grade }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Jurusan:</strong></td>
                        <td>{{ $class->major ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Wali Kelas:</strong></td>
                        <td>
                            @if($class->waliKelas)
                                <div>
                                    <strong>{{ $class->waliKelas->name }}</strong>
                                    <br><small class="text-muted">NIP: {{ $class->waliKelas->nip }}</small>
                                    <br><small class="text-muted">Email: {{ $class->waliKelas->email }}</small>
                                    @if($class->waliKelas->phone)
                                    <br><small class="text-muted">HP: {{ $class->waliKelas->phone }}</small>
                                    @endif
                                </div>
                            @else
                                <span class="text-muted">Belum ditentukan</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Kapasitas:</strong></td>
                        <td>{{ $class->capacity }} siswa</td>
                    </tr>
                    <tr>
                        <td><strong>Jumlah Siswa:</strong></td>
                        <td>
                            <span class="badge bg-primary">{{ $class->students->count() }} siswa</span>
                            @if($class->students->count() > $class->capacity)
                                <span class="badge bg-danger">Over Capacity</span>
                            @elseif($class->students->count() == $class->capacity)
                                <span class="badge bg-warning">Full</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Dibuat:</strong></td>
                        <td>{{ $class->created_at->format('d F Y H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-chart-pie"></i> Statistik Kelas</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted">Siswa Aktif:</small>
                    <h4 class="text-success">{{ $class->students->where('status', 'active')->count() }}</h4>
                </div>
                <div class="mb-3">
                    <small class="text-muted">Siswa Tidak Aktif:</small>
                    <h4 class="text-warning">{{ $class->students->where('status', 'inactive')->count() }}</h4>
                </div>
                <div class="mb-3">
                    <small class="text-muted">Siswa Lulus:</small>
                    <h4 class="text-info">{{ $class->students->where('status', 'graduated')->count() }}</h4>
                </div>
                <div class="mb-3">
                    <small class="text-muted">Sisa Kapasitas:</small>
                    <h4 class="text-primary">{{ max(0, $class->capacity - $class->students->where('status', 'active')->count()) }}</h4>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card mt-3">
            <div class="card-header">
                <h5><i class="fas fa-bolt"></i> Aksi Cepat</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.students.create') }}?class_id={{ $class->id }}" class="btn btn-success">
                        <i class="fas fa-user-plus"></i> Tambah Siswa ke Kelas Ini
                    </a>
                    @if($class->waliKelas)
                    <a href="{{ route('admin.users.show', $class->waliKelas) }}" class="btn btn-info">
                        <i class="fas fa-user"></i> Lihat Profile Wali Kelas
                    </a>
                    @endif
                    <a href="{{ route('admin.reports.attendance') }}?class_id={{ $class->id }}" class="btn btn-primary">
                        <i class="fas fa-chart-bar"></i> Lihat Absensi Kelas
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Students List -->
@if($class->students->count() > 0)
<div class="card mt-4">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-users"></i> Daftar Siswa ({{ $class->students->count() }})</h5>
            <div>
                <div class="btn-group btn-group-sm">
                    <button type="button" class="btn btn-outline-primary active" data-filter="all">
                        Semua ({{ $class->students->count() }})
                    </button>
                    <button type="button" class="btn btn-outline-success" data-filter="active">
                        Aktif ({{ $class->students->where('status', 'active')->count() }})
                    </button>
                    <button type="button" class="btn btn-outline-warning" data-filter="inactive">
                        Tidak Aktif ({{ $class->students->where('status', 'inactive')->count() }})
                    </button>
                    <button type="button" class="btn btn-outline-info" data-filter="graduated">
                        Lulus ({{ $class->students->where('status', 'graduated')->count() }})
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>NIS</th>
                        <th>Nama Siswa</th>
                        <th>L/P</th>
                        <th>Tempat, Tanggal Lahir</th>
                        <th>Orang Tua</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($class->students as $student)
                    <tr data-status="{{ $student->status }}">
                        <td>{{ $loop->iteration }}</td>
                        <td><strong>{{ $student->nis }}</strong></td>
                        <td>{{ $student->name }}</td>
                        <td>
                            <span class="badge bg-{{ $student->gender == 'L' ? 'primary' : 'info' }}">
                                {{ $student->gender }}
                            </span>
                        </td>
                        <td>
                            {{ $student->birth_place }}, {{ $student->birth_date->format('d/m/Y') }}
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
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@else
<div class="card mt-4">
    <div class="card-body text-center">
        <i class="fas fa-users fa-3x text-muted mb-3"></i>
        <h5>Belum Ada Siswa</h5>
        <p class="text-muted">Kelas ini belum memiliki siswa. Tambahkan siswa untuk mulai mengelola kelas.</p>
        <a href="{{ route('admin.students.create') }}?class_id={{ $class->id }}" class="btn btn-success">
            <i class="fas fa-plus"></i> Tambah Siswa Pertama
        </a>
    </div>
</div>
@endif
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterButtons = document.querySelectorAll('[data-filter]');
    const tableRows = document.querySelectorAll('tbody tr[data-status]');
    
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            const filter = this.getAttribute('data-filter');
            
            // Update active button
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            // Filter table rows
            tableRows.forEach(row => {
                const status = row.getAttribute('data-status');
                if (filter === 'all' || status === filter) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });
});
</script>
@endsection