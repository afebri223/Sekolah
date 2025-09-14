<!-- resources/views/kepala-sekolah/reports/index.blade.php -->
@extends('layouts.app')

@section('title', 'Laporan Absensi - Kepala Sekolah')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2><i class="fas fa-chart-line"></i> Pusat Laporan Absensi</h2>
                <p class="text-muted">Akses lengkap ke semua laporan dan analisis kehadiran siswa</p>
            </div>
            <div>
                <a href="{{ route('kepala-sekolah.dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Quick Access Cards -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card border-primary h-100">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-calendar-day"></i> Laporan Harian</h5>
            </div>
            <div class="card-body d-flex flex-column">
                <p class="card-text flex-grow-1">Monitor absensi siswa per hari dengan filter kelas dan analisis detail kehadiran real-time.</p>
                
                <form action="{{ route('kepala-sekolah.reports.daily') }}" method="GET" class="mb-3">
                    <div class="row g-2">
                        <div class="col-12">
                            <label class="form-label">Tanggal</label>
                            <input type="date" name="date" class="form-control" 
                                   value="{{ $selectedDate ?? today()->format('Y-m-d') }}" 
                                   max="{{ today()->format('Y-m-d') }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Filter Kelas</label>
                            <select name="class_id" class="form-select">
                                <option value="all" {{ ($selectedClass ?? 'all') == 'all' ? 'selected' : '' }}>Semua Kelas</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}" {{ ($selectedClass ?? '') == $class->id ? 'selected' : '' }}>
                                        {{ $class->name }} ({{ $class->grade }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 mt-3">
                        <i class="fas fa-search"></i> Lihat Laporan Harian
                    </button>
                </form>
                
                <div class="border-top pt-3">
                    <small class="text-muted">
                        <i class="fas fa-info-circle"></i> 
                        Menampilkan detail absensi per siswa, statistik kelas, dan analisis kehadiran harian.
                    </small>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card border-success h-100">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-calendar-alt"></i> Laporan Bulanan</h5>
            </div>
            <div class="card-body d-flex flex-column">
                <p class="card-text flex-grow-1">Analisis komprehensif kehadiran siswa per bulan dengan tren dan performa kelas.</p>
                
                <form action="{{ route('kepala-sekolah.reports.monthly') }}" method="GET" class="mb-3">
                    <div class="row g-2">
                        <div class="col-12">
                            <label class="form-label">Bulan/Tahun</label>
                            <input type="month" name="month" class="form-control" 
                                   value="{{ $selectedDate ?? now()->format('Y-m') }}" 
                                   max="{{ now()->format('Y-m') }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Filter Kelas</label>
                            <select name="class_id" class="form-select">
                                <option value="all" {{ ($selectedClass ?? 'all') == 'all' ? 'selected' : '' }}>Semua Kelas</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}" {{ ($selectedClass ?? '') == $class->id ? 'selected' : '' }}>
                                        {{ $class->name }} ({{ $class->grade }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success w-100 mt-3">
                        <i class="fas fa-search"></i> Lihat Laporan Bulanan
                    </button>
                </form>
                
                <div class="border-top pt-3">
                    <small class="text-muted">
                        <i class="fas fa-info-circle"></i> 
                        Statistik bulanan, persentase kehadiran, dan perbandingan antar kelas.
                    </small>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card border-info h-100">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-calendar"></i> Laporan Tahunan</h5>
            </div>
            <div class="card-body d-flex flex-column">
                <p class="card-text flex-grow-1">Analisis tren tahunan dengan ranking performa kelas dan insights mendalam.</p>
                
                <form action="{{ route('kepala-sekolah.reports.yearly') }}" method="GET" class="mb-3">
                    <div class="row g-2">
                        <div class="col-12">
                            <label class="form-label">Tahun</label>
                            <select name="year" class="form-select">
                                @for($year = now()->year; $year >= now()->year - 3; $year--)
                                    <option value="{{ $year }}" {{ $year == now()->year ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Filter Kelas</label>
                            <select name="class_id" class="form-select">
                                <option value="all" {{ ($selectedClass ?? 'all') == 'all' ? 'selected' : '' }}>Semua Kelas</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}" {{ ($selectedClass ?? '') == $class->id ? 'selected' : '' }}>
                                        {{ $class->name }} ({{ $class->grade }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-info w-100 mt-3">
                        <i class="fas fa-search"></i> Lihat Laporan Tahunan
                    </button>
                </form>
                
                <div class="border-top pt-3">
                    <small class="text-muted">
                        <i class="fas fa-info-circle"></i> 
                        Tren tahunan, ranking kelas, dan analisis performa jangka panjang.
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Advanced Features -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-warning text-white">
                <h6 class="mb-0"><i class="fas fa-download"></i> Export & Backup</h6>
            </div>
            <div class="card-body">
                <p class="card-text">Download laporan dalam berbagai format untuk arsip dan analisis lebih lanjut.</p>
                
                <form action="{{ route('kepala-sekolah.reports.export') }}" method="POST" class="mb-3">
                    @csrf
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label">Jenis Laporan</label>
                            <select name="type" class="form-select" required>
                                <option value="">Pilih Jenis</option>
                                <option value="daily">Harian</option>
                                <option value="monthly">Bulanan</option>
                                <option value="yearly">Tahunan</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Format</label>
                            <select name="format" class="form-select" required>
                                <option value="">Pilih Format</option>
                                <option value="excel">Excel (.xlsx)</option>
                                <option value="pdf">PDF (.pdf)</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-warning w-100 mt-3">
                        <i class="fas fa-download"></i> Download Laporan
                    </button>
                </form>
                
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <strong>Info:</strong> Fitur export sedang dalam pengembangan. Akan segera tersedia!
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-dark text-white">
                <h6 class="mb-0"><i class="fas fa-chart-pie"></i> Quick Analytics</h6>
            </div>
            <div class="card-body">