<!-- resources/views/guru/reports/monthly.blade.php -->
@extends('layouts.app')

@section('title', 'Laporan Bulanan - ' . \Carbon\Carbon::parse($monthYear)->format('F Y'))

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2><i class="fas fa-calendar-alt"></i> Laporan Absensi Bulanan</h2>
                <p class="text-muted">{{ \Carbon\Carbon::parse($monthYear)->format('F Y') }}</p>
            </div>
            <div>
                <a href="{{ route('guru.reports.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="card mb-4">
    <div class="card-header">
        <h6 class="mb-0"><i class="fas fa-filter"></i> Filter Laporan</h6>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Bulan/Tahun</label>
                <input type="month" name="month" class="form-control" value="{{ $monthYear }}" max="{{ now()->format('Y-m') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Kelas</label>
                <select name="class_id" class="form-select">
                    <option value="all" {{ $classId == 'all' ? 'selected' : '' }}>Semua Kelas</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ $classId == $class->id ? 'selected' : '' }}>
                            {{ $class->name }} ({{ $class->grade }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search"></i> Filter
                </button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('guru.reports.daily', ['date' => now()->format('Y-m-d')]) }}" class="btn btn-info w-100">
                    <i class="fas fa-calendar-day"></i> Harian
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Overall Statistics -->
<div class="row mb-4">
    <div class="col-md-2">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <i class="fas fa-school fa-2x mb-2"></i>
                <h4>{{ $overallStats['total_classes'] }}</h4>
                <p class="mb-0">Kelas</p>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card bg-secondary text-white">
            <div class="card-body text-center">
                <i class="fas fa-clipboard-list fa-2x mb-2"></i>
                <h4>{{ $overallStats['total_records'] }}</h4>
                <p class="mb-0">Total Record</p>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <i class="fas fa-check-circle fa-2x mb-2"></i>
                <h4>{{ $overallStats['hadir'] }}</h4>
                <p class="mb-0">Hadir</p>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card bg-warning text-white">
            <div class="card-body text-center">
                <i class="fas fa-hand-paper fa-2x mb-2"></i>
                <h4>{{ $overallStats['izin'] }}</h4>
                <p class="mb-0">Izin</p>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <i class="fas fa-thermometer-half fa-2x mb-2"></i>
                <h4>{{ $overallStats['sakit'] }}</h4>
                <p class="mb-0">Sakit</p>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card bg-danger text-white">
            <div class="card-body text-center">
                <i class="fas fa-times-circle fa-2x mb-2"></i>
                <h4>{{ $overallStats['alpha'] }}</h4>
                <p class="mb-0">Alpha</p>
            </div>
        </div>
    </div>
</div>

<!-- Overall Attendance Rate -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1">Tingkat Kehadiran Keseluruhan</h5>
                        <p class="text-muted mb-0">Rata-rata kehadiran semua kelas bulan {{ \Carbon\Carbon::parse($monthYear)->format('F Y') }}</p>
                    </div>
                    <div class="text-end">
                        <h3 class="text-{{ $overallStats['attendance_rate'] >= 80 ? 'success' : ($overallStats['attendance_rate'] >= 60 ? 'warning' : 'danger') }} mb-1">
                            {{ $overallStats['attendance_rate'] }}%
                        </h3>
                        <small class="text-muted">{{ $overallStats['hadir'] }}/{{ $overallStats['total_records'] }}</small>
                    </div>
                </div>