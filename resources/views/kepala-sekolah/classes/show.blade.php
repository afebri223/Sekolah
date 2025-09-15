<!-- resources/views/kepala-sekolah/classes/show.blade.php -->
@extends('layouts.app')

@section('title', 'Detail Kelas - ' . $class->name)

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2><i class="fas fa-door-open"></i> Detail Kelas {{ $class->name }}</h2>
                <p class="text-muted">{{ $class->grade }} {{ $class->major }} - Informasi lengkap dan statistik kelas</p>
            </div>
            <div>
                <a href="{{ route('kepala-sekolah.classes.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali ke Daftar Kelas
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Class Information -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0"><i class="fas fa-info-circle"></i> Informasi Kelas</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <td width="40%"><strong>Nama Kelas:</strong></td>
                        <td>{{ $class->name }}</td>
                    </tr>
                    <tr>
                        <td><strong>Tingkat:</strong></td>
                        <td><span class="badge bg-primary">{{ $class->grade }}</span></td>
                    </tr>
                    @if($class->major)
                    <tr>
                        <td><strong>Jurusan:</strong></td>
                        <td>{{ $class->major }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td><strong>Kapasitas:</strong></td>
                        <td>
                            {{ $class->capacity }} siswa
                            @php
                                $occupancy = $class->capacity > 0 ? ($class->students->count() / $class->capacity) * 100 : 0;
                            @endphp
                            <div class="progress mt-1" style="height: 4px;">
                                <div class="progress-bar bg-{{ $occupancy >= 90 ? 'danger' : ($occupancy >= 75 ? 'warning' : 'success') }}" 
                                     style="width: {{ $occupancy }}%"></div>
                            </div>
                            <small class="text-muted">{{ round($occupancy, 1) }}% terisi</small>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Jumlah Siswa:</strong></td>
                        <td>
                            <span class="badge bg-success">{{ $class->students->count() }} siswa aktif</span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        
        <!-- Wali Kelas Info -->
        <div class="card mt-3">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0"><i class="fas fa-chalkboard-teacher"></i> Wali Kelas</h6>
            </div>
            <div class="card-body">
                @if($class->waliKelas)
                    <div class="text-center mb-3">
                        <div class="avatar-xl mx-auto mb-3">
                            <span class="avatar-title bg-success text-white rounded-circle fs-2">
                                {{ strtoupper(substr($class->waliKelas->name, 0, 1)) }}
                            </span>
                        </div>
                        <h6>{{ $class->waliKelas->name }}</h6>
                        <p class="text-muted mb-0">{{ $class->waliKelas->email }}</p>
                        @if($class->waliKelas->phone)
                            <p class="text-muted mb-0">{{ $class->waliKelas->phone }}</p>
                        @endif
                        @if($class->waliKelas->nip)
                            <small class="text-muted">NIP: {{ $class->waliKelas->nip }}</small>
                        @endif
                    </div>
                    
                    <div class="border-top pt-3">
                        <small class="text-muted">
                            <i class="fas fa-calendar"></i> Menjadi wali kelas sejak kelas ini dibuat
                        </small>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-user-slash fa-3x text-muted mb-3"></i>
                        <h6 class="text-muted">Belum Ada Wali Kelas</h6>
                        <p class="text-muted">Kelas ini belum memiliki wali kelas yang ditugaskan.</p>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="card mt-3">
            <div class="card-header bg-warning text-white">
                <h6 class="mb-0"><i class="fas fa-bolt"></i> Aksi Cepat</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('kepala-sekolah.reports.daily', ['class_id' => $class->id, 'date' => today()->format('Y-m-d')]) }}" 
                       class="btn btn-primary">
                        <i class="fas fa-calendar-day"></i> Laporan Hari Ini
                    </a>
                    <a href="{{ route('kepala-sekolah.reports.monthly', ['class_id' => $class->id, 'month' => now()->format('Y-m')]) }}" 
                       class="btn btn-success">
                        <i class="fas fa-calendar-alt"></i> Laporan Bulanan
                    </a>
                    <a href="{{ route('kepala-sekolah.reports.yearly', ['class_id' => $class->id, 'year' => now()->year]) }}" 
                       class="btn btn-info">
                        <i class="fas fa-calendar"></i> Laporan Tahunan
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Statistics and Students -->
    <div class="col-md-8">
        <!-- Attendance Statistics -->
        <div class="card mb-3">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-chart-bar"></i> Statistik Kehadiran</h6>
            </div>
            <div class="card-body">
                <div class="row text-center mb-3">
                    @php
                        $todayAttendance = $class->attendances()->whereDate('date', today())->count();
                        $todayPresent = $class->attendances()->whereDate('date', today())->where('status', 'hadir')->count();
                        $todayIzin = $class->attendances()->whereDate('date', today())->where('status', 'izin')->count();
                        $todaySakit = $class->attendances()->whereDate('date', today())->where('status', 'sakit')->count();
                        $todayAlpha = $class->attendances()->whereDate('date', today())->where('status', 'alpha')->count();
                        $attendanceRate = $todayAttendance > 0 ? round(($todayPresent / $todayAttendance) * 100, 1) : 0;
                    @endphp
                    
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h4>{{ $todayPresent }}</h4>
                                <p class="mb-0">Hadir Hari Ini</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <h4>{{ $todayIzin }}</h4>
                                <p class="mb-0">Izin</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h4>{{ $todaySakit }}</h4>
                                <p class="mb-0">Sakit</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-danger text-white">
                            <div class="card-body">
                                <h4>{{ $todayAlpha }}</h4>
                                <p class="mb-0">Alpha</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Attendance Rate -->
                <div class="text-center">
                    <h5 class="text-{{ $attendanceRate >= 85 ? 'success' : ($attendanceRate >= 70 ? 'warning' : 'danger') }}">
                        Tingkat Kehadiran: {{ $attendanceRate }}%
                    </h5>
                    <div class="progress" style="height: 10px;">
                        <div class="progress-bar bg-{{ $attendanceRate >= 85 ? 'success' : ($attendanceRate >= 70 ? 'warning' : 'danger') }}" 
                             style="width: {{ $attendanceRate }}%"></div>
                    </div>
                    <small class="text-muted">{{ $todayPresent }}/{{ $todayAttendance }} siswa hadir hari ini</small>
                </div>
            </div>
        </div>
        
        <!-- Recent Attendance Summary -->
        <div class="card mb-3">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-history"></i> Ringkasan Kehadiran (30 Hari Terakhir)</h6>
            </div>
            <div class="card-body">
                @php
                    $recentAttendances = collect($recentAttendance ?? []);
                    $totalRecent = $recentAttendances->sum();
                @endphp
                
                @if($totalRecent > 0)
                    <div class="row text-center">
                        <div class="col-md-3">
                            <h6 class="text-success">{{ $recentAttendances['hadir'] ?? 0 }}</h6>
                            <small class="text-muted">Hadir</small>
                            <div class="progress mt-1" style="height: 4px;">
                                <div class="progress-bar bg-success" style="width: {{ ($recentAttendances['hadir'] ?? 0) / $totalRecent * 100 }}%"></div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <h6 class="text-warning">{{ $recentAttendances['izin'] ?? 0 }}</h6>
                            <small class="text-muted">Izin</small>
                            <div class="progress mt-1" style="height: 4px;">
                                <div class="progress-bar bg-warning" style="width: {{ ($recentAttendances['izin'] ?? 0) / $totalRecent * 100 }}%"></div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <h6 class="text-info">{{ $recentAttendances['sakit'] ?? 0 }}</h6>
                            <small class="text-muted">Sakit</small>
                            <div class="progress mt-1" style="height: 4px;">
                                <div class="progress-bar bg-info" style="width: {{ ($recentAttendances['sakit'] ?? 0) / $totalRecent * 100 }}%"></div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <h6 class="text-danger">{{ $recentAttendances['alpha'] ?? 0 }}</h6>
                            <small class="text-muted">Alpha</small>
                            <div class="progress mt-1" style="height: 4px;">
                                <div class="progress-bar bg-danger" style="width: {{ ($recentAttendances['alpha'] ?? 0) / $totalRecent * 100 }}%"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-center mt-3">
                        @php
                            $recentRate = $totalRecent > 0 ? round((($recentAttendances['hadir'] ?? 0) / $totalRecent) * 100, 1) : 0;
                        @endphp
                        <span class="badge bg-{{ $recentRate >= 85 ? 'success' : ($recentRate >= 70 ? 'warning' : 'danger') }} fs-6">
                            {{ $recentRate }}% tingkat kehadiran 30 hari terakhir
                        </span>
                    </div>
                @else
                    <div class="text-center text-muted">
                        <i class="fas fa-calendar-times fa-2x mb-2"></i>
                        <p class="mb-0">Belum ada data absensi dalam 30 hari terakhir</p>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Students List -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="fas fa-users"></i> Daftar Siswa ({{ $class->students->count() }} siswa)</h6>
                <div>
                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#studentsList">
                        <i class="fas fa-eye"></i> Toggle Daftar
                    </button>
                </div>
            </div>
            
            <div class="collapse show" id="studentsList">
                <div class="card-body">
                    @if($class->students->count() > 0)
                        <div class="row">
                            @foreach($class->students->sortBy('name') as $student)
                                <div class="col-md-6 mb-3">
                                    <div class="card border">
                                        <div class="card-body p-3">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm me-3">
                                                    <span class="avatar-title bg-primary text-white rounded-circle">
                                                        {{ strtoupper(substr($student->name, 0, 1)) }}
                                                    </span>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1">{{ $student->name }}</h6>
                                                    <p class="text-muted mb-1">
                                                        <small>
                                                            <i class="fas fa-id-badge"></i> {{ $student->nis }} |
                                                            <i class="fas fa-{{ $student->gender == 'L' ? 'male' : 'female' }}"></i> {{ $student->gender_full }}
                                                        </small>
                                                    </p>
                                                    
                                                    <!-- Recent attendance status -->
                                                    @php
                                                        $lastAttendance = $student->attendances()->latest()->first();
                                                    @endphp
                                                    @if($lastAttendance)
                                                        <span class="badge bg-{{ $lastAttendance->status_color }}">
                                                            Last: {{ ucfirst($lastAttendance->status) }} ({{ $lastAttendance->date->format('d/m') }})
                                                        </span>
                                                    @else
                                                        <span class="badge bg-secondary">Belum ada absensi</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-user-slash fa-3x text-muted mb-3"></i>
                            <h6 class="text-muted">Belum ada siswa</h6>
                            <p class="text-muted">Kelas ini belum memiliki siswa yang terdaftar.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add any interactive functionality here if needed
    console.log('Class detail page loaded for {{ $class->name }}');
});
</script>
@endpush
@endsection