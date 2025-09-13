@extends('layouts.app')

@section('title', 'Laporan Harian')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2><i class="fas fa-calendar-day"></i> Laporan Absensi Harian</h2>
                <p class="text-muted">{{ \Carbon\Carbon::parse($date)->format('l, d F Y') }}</p>
            </div>
            <div>
                <a href="{{ route('admin.reports.attendance') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                <button onclick="window.print()" class="btn btn-success">
                    <i class="fas fa-print"></i> Print
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Date Selector -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Pilih Tanggal</label>
                <input type="date" name="date" class="form-control" value="{{ $date }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Tampilkan
                </button>
            </div>
            <div class="col-md-6 text-end">
                <div class="btn-group">
                    <a href="?date={{ \Carbon\Carbon::parse($date)->subDay()->format('Y-m-d') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-chevron-left"></i> Kemarin
                    </a>
                    <a href="?date={{ today()->format('Y-m-d') }}" class="btn btn-outline-primary">
                        Hari Ini
                    </a>
                    <a href="?date={{ \Carbon\Carbon::parse($date)->addDay()->format('Y-m-d') }}" class="btn btn-outline-secondary">
                        Besok <i class="fas fa-chevron-right"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Overall Statistics -->
<div class="row mb-4">
    @php
        $overallStats = [
            'total_students' => collect($dailyData)->sum('total_students'),
            'total_attended' => collect($dailyData)->sum('attended'),
            'total_absent' => collect($dailyData)->sum('absent'),
            'total_classes' => count($dailyData)
        ];
        $attendanceRate = $overallStats['total_students'] > 0 ? 
            round(($overallStats['total_attended'] / $overallStats['total_students']) * 100, 1) : 0;
    @endphp
    
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <h3>{{ $overallStats['total_classes'] }}</h3>
                <small>Total Kelas</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <h3>{{ $overallStats['total_students'] }}</h3>
                <small>Total Siswa</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <h3>{{ $overallStats['total_attended'] }}</h3>
                <small>Sudah Absen</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body text-center">
                <h3>{{ $overallStats['total_absent'] }}</h3>
                <small>Belum Absen</small>
            </div>
        </div>
    </div>
</div>

@foreach($dailyData as $classData)
<div class="card mb-4">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-door-open"></i> Kelas {{ $classData['class']->name }}
            </h5>
            <div>
                @php
                    $classRate = $classData['total_students'] > 0 ? 
                        round(($classData['attended'] / $classData['total_students']) * 100, 1) : 0;
                @endphp
                <span class="badge bg-{{ $classRate >= 80 ? 'success' : ($classRate >= 60 ? 'warning' : 'danger') }} fs-6">
                    {{ $classRate }}% Sudah Absen
                </span>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-3">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="fas fa-users fa-2x text-primary"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">{{ $classData['total_students'] }}</h5>
                        <small class="text-muted">Total Siswa</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="fas fa-check-circle fa-2x text-success"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">{{ $classData['attended'] }}</h5>
                        <small class="text-muted">Sudah Absen</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="fas fa-times-circle fa-2x text-warning"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">{{ $classData['absent'] }}</h5>
                        <small class="text-muted">Belum Absen</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="progress" style="height: 20px;">
                    <div class="progress-bar bg-{{ $classRate >= 80 ? 'success' : ($classRate >= 60 ? 'warning' : 'danger') }}" 
                         style="width: {{ $classRate }}%">
                        {{ $classRate }}%
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-sm">
                <thead>
                    <tr>
                        <th style="width: 5%">No</th>
                        <th style="width: 15%">NIS</th>
                        <th style="width: 30%">Nama Siswa</th>
                        <th style="width: 15%" class="text-center">Status</th>
                        <th style="width: 35%">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($classData['students'] as $studentData)
                    <tr class="{{ $studentData['status'] == 'belum_absen' ? 'table-warning' : '' }}">
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $studentData['student']->nis }}</td>
                        <td>{{ $studentData['student']->name }}</td>
                        <td class="text-center">
                            @if($studentData['status'] == 'belum_absen')
                                <span class="badge bg-secondary">
                                    <i class="fas fa-clock"></i> Belum Absen
                                </span>
                            @else
                                @php
                                    $colors = [
                                        'hadir' => 'success',
                                        'izin' => 'warning', 
                                        'sakit' => 'info',
                                        'alpha' => 'danger'
                                    ];
                                    $icons = [
                                        'hadir' => 'check',
                                        'izin' => 'exclamation-triangle',
                                        'sakit' => 'heartbeat',
                                        'alpha' => 'times'
                                    ];
                                @endphp
                                <span class="badge bg-{{ $colors[$studentData['status']] }}">
                                    <i class="fas fa-{{ $icons[$studentData['status']] }}"></i>
                                    {{ ucfirst($studentData['status']) }}
                                </span>
                            @endif
                        </td>
                        <td>
                            {{ $studentData['notes'] ?: '-' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endforeach

@if(count($dailyData) == 0)
<div class="card">
    <div class="card-body text-center py-5">
        <i class="fas fa-calendar-day fa-3x text-muted mb-3"></i>
        <h4>Tidak ada data kelas</h4>
        <p class="text-muted">Belum ada kelas yang dibuat di sistem</p>
        <a href="{{ route('admin.classes.index') }}" class="btn btn-primary">Kelola Kelas</a>
    </div>
</div>
@endif
@endsection