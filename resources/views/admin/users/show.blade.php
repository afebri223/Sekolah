@extends('layouts.app')

@section('title', 'Detail User')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2><i class="fas fa-user"></i> Detail User</h2>
                <p class="text-muted">Informasi lengkap pengguna sistem</p>
            </div>
            <div>
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Edit
                </a>
                @if($user->id !== auth()->id())
                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" 
                      style="display: inline-block;" 
                      onsubmit="return confirm('Yakin ingin menghapus user ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-info-circle"></i> Informasi User</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <td width="30%"><strong>Nama Lengkap:</strong></td>
                        <td>{{ $user->name }}</td>
                    </tr>
                    <tr>
                        <td><strong>Email:</strong></td>
                        <td>{{ $user->email }}</td>
                    </tr>
                    <tr>
                        <td><strong>Role:</strong></td>
                        <td>
                            <span class="badge bg-{{ $user->role == 'admin' ? 'danger' : ($user->role == 'kepala_sekolah' ? 'success' : 'primary') }}">
                                {{ $user->getRoleName() }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>NIP:</strong></td>
                        <td>{{ $user->nip ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Nomor Telepon:</strong></td>
                        <td>{{ $user->phone ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Status Wali Kelas:</strong></td>
                        <td>
                            @if($user->is_wali_kelas)
                                <span class="badge bg-success">Ya</span>
                                @if($user->waliKelas)
                                    - Kelas {{ $user->waliKelas->name }}
                                @endif
                            @else
                                <span class="badge bg-secondary">Tidak</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Bergabung:</strong></td>
                        <td>{{ $user->created_at->format('d F Y H:i') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Terakhir Update:</strong></td>
                        <td>{{ $user->updated_at->format('d F Y H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        @if($user->isWaliKelas() && $user->waliKelas)
        <div class="card mt-4">
            <div class="card-header">
                <h5><i class="fas fa-users"></i> Data Kelas yang Diampu</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <td width="30%"><strong>Nama Kelas:</strong></td>
                        <td>{{ $user->waliKelas->name }}</td>
                    </tr>
                    <tr>
                        <td><strong>Tingkat:</strong></td>
                        <td>{{ $user->waliKelas->grade }}</td>
                    </tr>
                    @if($user->waliKelas->major)
                    <tr>
                        <td><strong>Jurusan:</strong></td>
                        <td>{{ $user->waliKelas->major }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td><strong>Kapasitas:</strong></td>
                        <td>{{ $user->waliKelas->capacity }} siswa</td>
                    </tr>
                    <tr>
                        <td><strong>Jumlah Siswa:</strong></td>
                        <td>{{ $user->waliKelas->students->count() }} siswa</td>
                    </tr>
                </table>

                @if($user->waliKelas->students->count() > 0)
                <div class="mt-3">
                    <h6>Daftar Siswa:</h6>
                    <div class="table-responsive">
                        <table class="table table-striped table-sm">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>NIS</th>
                                    <th>Nama</th>
                                    <th>L/P</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($user->waliKelas->students->take(10) as $student)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $student->nis }}</td>
                                    <td>{{ $student->name }}</td>
                                    <td>{{ $student->gender }}</td>
                                    <td>
                                        <span class="badge bg-{{ $student->status == 'active' ? 'success' : 'secondary' }}">
                                            {{ ucfirst($student->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                                @if($user->waliKelas->students->count() > 10)
                                <tr>
                                    <td colspan="5" class="text-center">
                                        <small class="text-muted">Dan {{ $user->waliKelas->students->count() - 10 }} siswa lainnya...</small>
                                    </td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-chart-bar"></i> Statistik Aktivitas</h5>
            </div>
            <div class="card-body">
                @if($user->isWaliKelas())
                <div class="mb-3">
                    <small class="text-muted">Total Absensi Dicatat:</small>
                    <h4 class="text-primary">{{ $user->attendanceRecords->count() }}</h4>
                </div>
                <div class="mb-3">
                    <small class="text-muted">Absensi Bulan Ini:</small>
                    <h4 class="text-success">{{ $user->attendanceRecords->where('created_at', '>=', now()->startOfMonth())->count() }}</h4>
                </div>
                @if($user->attendanceRecords->count() > 0)
                <div class="mb-3">
                    <small class="text-muted">Terakhir Input Absensi:</small>
                    <p class="mb-0">{{ $user->attendanceRecords->sortByDesc('created_at')->first()->created_at->format('d/m/Y H:i') }}</p>
                </div>
                @endif
                @else
                <div class="text-center text-muted">
                    <i class="fas fa-info-circle fa-2x mb-2"></i>
                    <p>Statistik aktivitas hanya tersedia untuk wali kelas</p>
                </div>
                @endif
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h5><i class="fas fa-history"></i> Riwayat Terakhir</h5>
            </div>
            <div class="card-body">
                @if($user->attendanceRecords->count() > 0)
                <div class="timeline">
                    @foreach($user->attendanceRecords->sortByDesc('created_at')->take(5) as $attendance)
                    <div class="timeline-item">
                        <div class="timeline-date">
                            {{ $attendance->created_at->format('d/m') }}
                        </div>
                        <div class="timeline-content">
                            <small class="text-muted">Input absensi</small>
                            <div>{{ $attendance->student->name }}</div>
                            <span class="badge bg-{{ $attendance->status_color }} badge-sm">
                                {{ ucfirst($attendance->status) }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center text-muted">
                    <i class="fas fa-clipboard-list fa-2x mb-2"></i>
                    <p class="mb-0">Belum ada aktivitas absensi</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 10px;
    top: 0;
    bottom: 0;
    width: 2px;
    background-color: #dee2e6;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-item::before {
    content: '';
    position: absolute;
    left: -24px;
    top: 5px;
    width: 8px;
    height: 8px;
    background-color: #007bff;
    border-radius: 50%;
    border: 2px solid white;
}

.timeline-date {
    font-size: 11px;
    color: #6c757d;
    font-weight: 600;
}

.timeline-content {
    font-size: 13px;
}

.badge-sm {
    font-size: 10px;
}
</style>
@endsection