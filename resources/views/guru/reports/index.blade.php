<!-- resources/views/guru/reports/index.blade.php -->
@extends('layouts.app')

@section('title', 'Laporan Absensi')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2><i class="fas fa-chart-line"></i> Laporan Absensi</h2>
                <p class="text-muted">Lihat laporan harian dan bulanan seluruh kelas</p>
            </div>
            <div>
                <a href="{{ route('guru.dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Quick Access Cards -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card border-primary">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-calendar-day"></i> Laporan Harian</h5>
            </div>
            <div class="card-body">
                <p class="card-text">Lihat absensi siswa untuk tanggal tertentu dengan filter kelas.</p>
                
                <form action="{{ route('guru.reports.daily') }}" method="GET" class="mb-3">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <input type="date" name="date" class="form-control" 
                                   value="{{ request('date', today()->format('Y-m-d')) }}" 
                                   max="{{ today()->format('Y-m-d') }}">
                        </div>
                        <div class="col-md-6">
                            <select name="class_id" class="form-select">
                                <option value="all">Semua Kelas</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}">
                                        {{ $class->name }} ({{ $class->students->count() }} siswa)
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary mt-2 w-100">
                        <i class="fas fa-search"></i> Lihat Laporan Harian
                    </button>
                </form>
                
                <div class="border-top pt-3">
                    <small class="text-muted">
                        <i class="fas fa-info-circle"></i> 
                        Laporan harian menampilkan detail absensi per siswa untuk tanggal yang dipilih.
                    </small>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card border-success">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-calendar-alt"></i> Laporan Bulanan</h5>
            </div>
            <div class="card-body">
                <p class="card-text">Lihat ringkasan absensi siswa untuk bulan tertentu dengan statistik lengkap.</p>
                
                <form action="{{ route('guru.reports.monthly') }}" method="GET" class="mb-3">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <input type="month" name="month" class="form-control" 
                                   value="{{ request('month', now()->format('Y-m')) }}" 
                                   max="{{ now()->format('Y-m') }}">
                        </div>
                        <div class="col-md-6">
                            <select name="class_id" class="form-select">
                                <option value="all">Semua Kelas</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}">
                                        {{ $class->name }} ({{ $class->students->count() }} siswa)
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success mt-2 w-100">
                        <i class="fas fa-search"></i> Lihat Laporan Bulanan
                    </button>
                </form>
                
                <div class="border-top pt-3">
                    <small class="text-muted">
                        <i class="fas fa-info-circle"></i> 
                        Laporan bulanan menampilkan statistik kehadiran dan persentase per kelas.
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Classes Overview -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-school"></i> Data Kelas yang Tersedia</h5>
    </div>
    <div class="card-body">
        @if($classes->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Kelas</th>
                            <th>Tingkat</th>
                            <th>Jurusan</th>
                            <th>Wali Kelas</th>
                            <th>Jumlah Siswa</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($classes as $class)
                            <tr>
                                <td>
                                    <strong>{{ $class->name }}</strong>
                                </td>
                                <td>
                                    <span class="badge bg-primary">{{ $class->grade }}</span>
                                </td>
                                <td>{{ $class->major ?? '-' }}</td>
                                <td>
                                    @if($class->waliKelas)
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-2">
                                                <span class="avatar-title bg-success text-white rounded-circle fs-6">
                                                    {{ strtoupper(substr($class->waliKelas->name, 0, 1)) }}
                                                </span>
                                            </div>
                                            <div>
                                                <small class="fw-bold">{{ $class->waliKelas->name }}</small>
                                                <br><small class="text-muted">{{ $class->waliKelas->email }}</small>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted">Belum ada wali kelas</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-info">
                                        {{ $class->students->count() }} / {{ $class->capacity }} siswa
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('guru.reports.daily', ['class_id' => $class->id, 'date' => today()->format('Y-m-d')]) }}" 
                                           class="btn btn-outline-primary" title="Laporan Harian">
                                            <i class="fas fa-calendar-day"></i>
                                        </a>
                                        <a href="{{ route('guru.reports.monthly', ['class_id' => $class->id, 'month' => now()->format('Y-m')]) }}" 
                                           class="btn btn-outline-success" title="Laporan Bulanan">
                                            <i class="fas fa-calendar-alt"></i>
                                        </a>
                                        <a href="{{ route('guru.reports.class.detail', $class->id) }}" 
                                           class="btn btn-outline-info" title="Detail Kelas">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-school fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Belum ada data kelas</h5>
                <p class="text-muted">Hubungi admin untuk menambahkan kelas.</p>
            </div>
        @endif
    </div>
</div>

<!-- Quick Stats Today -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card bg-light">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-chart-bar"></i> Statistik Cepat Hari Ini</h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3">
                        <div class="border rounded p-3 bg-white">
                            <h4 class="text-primary mb-1">{{ $classes->count() }}</h4>
                            <small class="text-muted">Total Kelas</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border rounded p-3 bg-white">
                            <h4 class="text-success mb-1">{{ $classes->sum(function($class) { return $class->students->count(); }) }}</h4>
                            <small class="text-muted">Total Siswa Aktif</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border rounded p-3 bg-white">
                            <h4 class="text-info mb-1">{{ $classes->whereNotNull('wali_kelas_id')->count() }}</h4>
                            <small class="text-muted">Kelas Ber-Wali</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border rounded p-3 bg-white">
                            <h4 class="text-warning mb-1" id="todayAttendance">-</h4>
                            <small class="text-muted">Absensi Hari Ini</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Load today's attendance count
    fetch(`{{ route('guru.reports.daily') }}?date={{ today()->format('Y-m-d') }}&ajax=1`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('todayAttendance').textContent = data.total_records || '0';
        })
        .catch(error => {
            document.getElementById('todayAttendance').textContent = '0';
        });
        
    // Form validation
    document.querySelectorAll('form').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            const dateInput = form.querySelector('input[type="date"]');
            const monthInput = form.querySelector('input[type="month"]');
            
            if (dateInput && !dateInput.value) {
                e.preventDefault();
                alert('Silakan pilih tanggal terlebih dahulu.');
                dateInput.focus();
                return;
            }
            
            if (monthInput && !monthInput.value) {
                e.preventDefault();
                alert('Silakan pilih bulan terlebih dahulu.');
                monthInput.focus();
                return;
            }
        });
    });
});
</script>
@endpush
@endsection