<!-- resources/views/admin/students/show.blade.php -->
@extends('layouts.app')

@section('title', 'Detail Siswa')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2><i class="fas fa-user-graduate"></i> Detail Siswa</h2>
                <p class="text-muted">Informasi lengkap data siswa</p>
            </div>
            <div>
                <a href="{{ route('admin.students.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                <a href="{{ route('admin.students.edit', $student) }}" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <form method="POST" action="{{ route('admin.students.destroy', $student) }}" 
                      style="display: inline-block;" 
                      onsubmit="return confirm('Yakin ingin menghapus siswa ini? Data absensi juga akan terhapus.')">
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
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-id-card"></i> Informasi Siswa</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td width="40%"><strong>NIS:</strong></td>
                                <td>{{ $student->nis }}</td>
                            </tr>
                            <tr>
                                <td><strong>Nama Lengkap:</strong></td>
                                <td>{{ $student->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Jenis Kelamin:</strong></td>
                                <td>
                                    <span class="badge bg-{{ $student->gender == 'L' ? 'primary' : 'info' }}">
                                        {{ $student->gender_full }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Tempat, Tgl Lahir:</strong></td>
                                <td>{{ $student->birth_place }}, {{ $student->birth_date->format('d F Y') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Umur:</strong></td>
                                <td>{{ $student->birth_date->age }} tahun</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td width="40%"><strong>Kelas:</strong></td>
                                <td>
                                    <span class="badge bg-secondary">{{ $student->class->name }}</span>
                                    {{ $student->class->grade }} {{ $student->class->major }}
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Wali Kelas:</strong></td>
                                <td>{{ $student->class->waliKelas->name ?? 'Belum ditentukan' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Status:</strong></td>
                                <td>
                                    <span class="badge bg-{{ $student->status == 'active' ? 'success' : ($student->status == 'graduated' ? 'info' : 'secondary') }}">
                                        {{ ucfirst($student->status) }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>HP Siswa:</strong></td>
                                <td>{{ $student->phone ?: '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Terdaftar:</strong></td>
                                <td>{{ $student->created_at->format('d F Y') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="mt-3">
                    <strong>Alamat:</strong>
                    <p class="mb-0 mt-1">{{ $student->address }}</p>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h5><i class="fas fa-users"></i> Data Orang Tua/Wali</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <td width="30%"><strong>Nama Orang Tua/Wali:</strong></td>
                        <td>{{ $student->parent_name }}</td>
                    </tr>
                    <tr>
                        <td><strong>Nomor HP:</strong></td>
                        <td>
                            <a href="tel:{{ $student->parent_phone }}" class="text-decoration-none">
                                <i class="fas fa-phone text-success"></i> {{ $student->parent_phone }}
                            </a>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Riwayat Absensi Terbaru -->
        @if($student->attendances->count() > 0)
        <div class="card mt-4">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-calendar-check"></i> Riwayat Absensi Terbaru</h5>
                    <a href="{{ route('admin.reports.attendance') }}?class_id={{ $student->class_id }}" class="btn btn-sm btn-outline-primary">
                        Lihat Semua
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Status</th>
                                <th>Keterangan</th>
                                <th>Dicatat Oleh</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($student->attendances->sortByDesc('date')->take(10) as $attendance)
                            <tr>
                                <td>{{ $attendance->date->format('d/m/Y') }}</td>
                                <td>
                                    <span class="badge bg-{{ $attendance->status_color }}">
                                        {{ ucfirst($attendance->status) }}
                                    </span>
                                </td>
                                <td>{{ $attendance->notes ?: '-' }}</td>
                                <td>{{ $attendance->recordedBy->name }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-chart-pie"></i> Statistik Absensi</h5>
            </div>
            <div class="card-body">
                @if($student->attendances->count() > 0)
                <div class="mb-3">
                    <small class="text-muted">Total Absensi Tercatat:</small>
                    <h4 class="text-primary">{{ $student->attendances->count() }}</h4>
                </div>
                <div class="mb-3">
                    <small class="text-muted">Hadir:</small>
                    <h4 class="text-success">{{ $student->attendances->where('status', 'hadir')->count() }}</h4>
                </div>
                <div class="mb-3">
                    <small class="text-muted">Izin:</small>
                    <h4 class="text-warning">{{ $student->attendances->where('status', 'izin')->count() }}</h4>
                </div>
                <div class="mb-3">
                    <small class="text-muted">Sakit:</small>
                    <h4 class="text-info">{{ $student->attendances->where('status', 'sakit')->count() }}</h4>
                </div>
                <div class="mb-3">
                    <small class="text-muted">Alpha:</small>
                    <h4 class="text-danger">{{ $student->attendances->where('status', 'alpha')->count() }}</h4>
                </div>

                @php
                    $totalAttendance = $student->attendances->count();
                    $presentCount = $student->attendances->where('status', 'hadir')->count();
                    $attendanceRate = $totalAttendance > 0 ? ($presentCount / $totalAttendance) * 100 : 0;
                @endphp

                <div class="mt-4">
                    <small class="text-muted">Tingkat Kehadiran:</small>
                    <div class="progress mt-1" style="height: 20px;">
                        <div class="progress-bar bg-{{ $attendanceRate >= 80 ? 'success' : ($attendanceRate >= 60 ? 'warning' : 'danger') }}" 
                             role="progressbar" style="width: {{ $attendanceRate }}%">
                            {{ round($attendanceRate, 1) }}%
                        </div>
                    </div>
                </div>
                @else
                <div class="text-center text-muted">
                    <i class="fas fa-calendar-times fa-3x mb-3"></i>
                    <p>Belum ada data absensi</p>
                </div>
                @endif
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h5><i class="fas fa-bolt"></i> Aksi Cepat</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    @if($student->class->waliKelas)
                    <a href="{{ route('admin.users.show', $student->class->waliKelas) }}" class="btn btn-info">
                        <i class="fas fa-user"></i> Profile Wali Kelas
                    </a>
                    @endif
                    <a href="{{ route('admin.classes.show', $student->class) }}" class="btn btn-primary">
                        <i class="fas fa-door-open"></i> Detail Kelas {{ $student->class->name }}
                    </a>
                    <a href="{{ route('admin.reports.attendance') }}?class_id={{ $student->class_id }}&search={{ $student->nis }}" class="btn btn-success">
                        <i class="fas fa-chart-bar"></i> Laporan Absensi Siswa
                    </a>
                    <a href="tel:{{ $student->parent_phone }}" class="btn btn-warning">
                        <i class="fas fa-phone"></i> Hubungi Orang Tua
                    </a>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h5><i class="fas fa-info-circle"></i> Informasi Tambahan</h5>
            </div>
            <div class="card-body">
                <small class="text-muted">Data terakhir diupdate:</small>
                <p class="mb-2">{{ $student->updated_at->format('d F Y, H:i') }}</p>
                
                @if($student->attendances->count() > 0)
                <small class="text-muted">Absensi terakhir:</small>
                <p class="mb-0">{{ $student->attendances->sortByDesc('date')->first()->date->format('d F Y') }}</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection