<!-- resources/views/dashboards/wali-kelas.blade.php -->
@extends('layouts.app')

@section('title', 'Dashboard Wali Kelas')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2><i class="fas fa-users"></i> Dashboard Wali Kelas {{ $class->name }}</h2>
                <p class="text-muted">{{ $class->grade }} {{ $class->major }} - Kapasitas: {{ $class->capacity }} siswa</p>
            </div>
            <div>
                <a href="{{ route('attendance.index') }}" class="btn btn-primary">
                    <i class="fas fa-calendar-plus"></i> Input Absensi Hari Ini
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <i class="fas fa-users fa-3x mb-3"></i>
                <h3>{{ $students->count() }}</h3>
                <p>Total Siswa Aktif</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <i class="fas fa-check-circle fa-3x mb-3"></i>
                <h3>{{ $todayAttendances->where('status', 'hadir')->count() }}</h3>
                <p>Hadir Hari Ini</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body text-center">
                <i class="fas fa-exclamation-circle fa-3x mb-3"></i>
                <h3>{{ $todayAttendances->whereIn('status', ['izin', 'sakit'])->count() }}</h3>
                <p>Izin/Sakit</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-danger text-white">
            <div class="card-body text-center">
                <i class="fas fa-times-circle fa-3x mb-3"></i>
                <h3>{{ $todayAttendances->where('status', 'alpha')->count() }}</h3>
                <p>Alpha</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-list"></i> Daftar Siswa Kelas {{ $class->name }}</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>NIS</th>
                                <th>Nama</th>
                                <th>L/P</th>
                                <th>Status Hari Ini</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($students as $student)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $student->nis }}</td>
                                <td>{{ $student->name }}</td>
                                <td>{{ $student->gender }}</td>
                                <td>
                                    @php
                                        $todayAttendance = $todayAttendances->where('student_id', $student->id)->first();
                                    @endphp
                                    @if($todayAttendance)
                                        <span class="badge bg-{{ $todayAttendance->status_color }}">
                                            {{ ucfirst($todayAttendance->status) }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">Belum Absen</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">Tidak ada data siswa</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-tools"></i> Menu Wali Kelas</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('attendance.index') }}" class="btn btn-primary">
                        <i class="fas fa-calendar-plus"></i> Input Absensi
                    </a>
                    <a href="{{ route('attendance.history') }}" class="btn btn-info">
                        <i class="fas fa-history"></i> Riwayat Absensi
                    </a>
                    <a href="#" class="btn btn-success">
                        <i class="fas fa-chart-bar"></i> Laporan Bulanan
                    </a>
                    <a href="#" class="btn btn-warning">
                        <i class="fas fa-phone"></i> Kontak Orang Tua
                    </a>
                </div>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h5><i class="fas fa-info-circle"></i> Informasi Kelas</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr>
                        <td><strong>Nama Kelas:</strong></td>
                        <td>{{ $class->name }}</td>
                    </tr>
                    <tr>
                        <td><strong>Tingkat:</strong></td>
                        <td>{{ $class->grade }}</td>
                    </tr>
                    @if($class->major)
                    <tr>
                        <td><strong>Jurusan:</strong></td>
                        <td>{{ $class->major }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td><strong>Kapasitas:</strong></td>
                        <td>{{ $class->capacity }} siswa</td>
                    </tr>
                    <tr>
                        <td><strong>Jumlah Siswa:</strong></td>
                        <td>{{ $students->count() }} siswa</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection