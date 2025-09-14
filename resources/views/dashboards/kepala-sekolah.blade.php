<!-- resources/views/dashboards/kepala-sekolah.blade.php -->
@extends('layouts.app')

@section('title', 'Dashboard Kepala Sekolah')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2><i class="fas fa-school"></i> Dashboard Kepala Sekolah</h2>
                <p class="text-muted">Monitoring dan laporan seluruh aktivitas sekolah</p>
            </div>
            <div>
                <div class="btn-group">
                    <a href="{{ route('kepala-sekolah.reports.index') }}" class="btn btn-primary">
                        <i class="fas fa-chart-bar"></i> Lihat Laporan
                    </a>
                    <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown">
                        <span class="visually-hidden">Toggle Dropdown</span>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('kepala-sekolah.reports.daily') }}"><i class="fas fa-calendar-day"></i> Laporan Harian</a></li>
                        <li><a class="dropdown-item" href="{{ route('kepala-sekolah.reports.monthly') }}"><i class="fas fa-calendar-alt"></i> Laporan Bulanan</a></li>
                        <li><a class="dropdown-item" href="{{ route('kepala-sekolah.reports.yearly') }}"><i class="fas fa-calendar"></i> Laporan Tahunan</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <i class="fas fa-chalkboard-teacher fa-3x mb-3"></i>
                <h3>{{ $stats['total_guru'] }}</h3>
                <p>Total Guru</p>
                <small>Termasuk wali kelas</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <i class="fas fa-door-open fa-3x mb-3"></i>
                <h3>{{ $stats['total_kelas'] }}</h3>
                <p>Total Kelas</p>
                <small>Kelas aktif</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <i class="fas fa-user-graduate fa-3x mb-3"></i>
                <h3>{{ $stats['total_siswa'] }}</h3>
                <p>Total Siswa</p>
                <small>Siswa aktif</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body text-center">
                <i class="fas fa-calendar-check fa-3x mb-3"></i>
                <h3>{{ $stats['attendance_today'] }}</h3>
                <p>Absensi Hari Ini</p>
                <small>Total record</small>
            </div>
        </div>
    </div>
</div>

<!-- Quick Reports Section -->
<div class="row mb-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-chart-line"></i> Laporan Cepat</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card border-left-primary">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Hari Ini</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <a href="{{ route('kepala-sekolah.reports.daily', ['date' => today()->format('Y-m-d')]) }}" class="text-decoration-none">
                                                Lihat Detail <i class="fas fa-arrow-right"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-left-success">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Bulan Ini</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <a href="{{ route('kepala-sekolah.reports.monthly', ['month' => now()->format('Y-m')]) }}" class="text-decoration-none">
                                                Lihat Detail <i class="fas fa-arrow-right"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-left-info">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Tahun Ini</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <a href="{{ route('kepala-sekolah.reports.yearly', ['year' => now()->year]) }}" class="text-decoration-none">
                                                Lihat Detail <i class="fas fa-arrow-right"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-calendar fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-filter"></i> Filter Cepat</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('kepala-sekolah.reports.daily') }}" method="GET">
                    <div class="mb-3">
                        <label class="form-label">Pilih Tanggal</label>
                        <input type="date" name="date" class="form-control" value="{{ today()->format('Y-m-d') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Filter Kelas</label>
                        <select name="class_id" class="form-select">
                            <option value="all">Semua Kelas</option>
                            @foreach(\App\Models\Classes::orderBy('grade')->orderBy('name')->get() as $class)
                                <option value="{{ $class->id }}">{{ $class->name }} ({{ $class->grade }})</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> Lihat Laporan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Management Links -->
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-chart-pie"></i> Laporan & Analisis</h5>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <a href="{{ route('kepala-sekolah.reports.daily') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-calendar-day text-primary"></i> Laporan Harian
                        <small class="text-muted d-block">Monitoring absensi per hari dengan filter kelas</small>
                    </a>
                    <a href="{{ route('kepala-sekolah.reports.monthly') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-calendar-alt text-success"></i> Laporan Bulanan
                        <small class="text-muted d-block">Analisis kehadiran siswa per bulan</small>
                    </a>
                    <a href="{{ route('kepala-sekolah.reports.yearly') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-calendar text-info"></i> Laporan Tahunan
                        <small class="text-muted d-block">Ranking performa kelas dan trend tahunan</small>
                    </a>
                    <a href="{{ route('kepala-sekolah.reports.export') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-file-export text-warning"></i> Export Data
                        <small class="text-muted d-block">Download laporan dalam format Excel/PDF</small>
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-eye"></i> Data Monitoring</h5>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <a href="{{ route('kepala-sekolah.classes.index') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-door-open text-primary"></i> Data Kelas
                        <small class="text-muted d-block">Lihat semua kelas dan wali kelas</small>
                    </a>
                    <a href="{{ route('kepala-sekolah.students.index') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-user-graduate text-success"></i> Data Siswa
                        <small class="text-muted d-block">Monitoring data siswa seluruh kelas</small>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action">
                        <i class="fas fa-chalkboard-teacher text-info"></i> Performa Guru
                        <small class="text-muted d-block">Monitoring kinerja wali kelas</small>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action">
                        <i class="fas fa-trophy text-warning"></i> Prestasi Siswa
                        <small class="text-muted d-block">Tracking pencapaian dan prestasi</small>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-refresh stats every 5 minutes
    setInterval(function() {
        fetch('{{ route("api.dashboard.stats") }}')
            .then(response => response.json())
            .then(data => {
                // Update stats if needed
                console.log('Stats refreshed:', data);
            })
            .catch(error => console.log('Stats refresh failed:', error));
    }, 300000); // 5 minutes
});
</script>
@endpush
@endsection