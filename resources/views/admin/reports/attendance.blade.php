<!-- resources/views/admin/reports/attendance.blade.php -->
@extends('layouts.app')

@section('title', 'Laporan Absensi')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2><i class="fas fa-chart-bar"></i> Laporan Absensi</h2>
                <p class="text-muted">Laporan detail absensi siswa</p>
            </div>
            <div>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                <a href="{{ route('admin.reports.attendance.export') }}" class="btn btn-success">
                    <i class="fas fa-download"></i> Export Excel
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <h3>{{ $stats['hadir'] }}</h3>
                <p class="mb-0">Hadir</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body text-center">
                <h3>{{ $stats['izin'] }}</h3>
                <p class="mb-0">Izin</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <h3>{{ $stats['sakit'] }}</h3>
                <p class="mb-0">Sakit</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-danger text-white">
            <div class="card-body text-center">
                <h3>{{ $stats['alpha'] }}</h3>
                <p class="mb-0">Alpha</p>
            </div>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Tanggal Mulai</label>
                <input type="date" name="start_date" class="form-control" 
                       value="{{ request('start_date') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Tanggal Akhir</label>
                <input type="date" name="end_date" class="form-control" 
                       value="{{ request('end_date') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Kelas</label>
                <select name="class_id" class="form-select">
                    <option value="">Semua Kelas</option>
                    @foreach($classes as $class)
                    <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                        {{ $class->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua</option>
                    <option value="hadir" {{ request('status') == 'hadir' ? 'selected' : '' }}>Hadir</option>
                    <option value="izin" {{ request('status') == 'izin' ? 'selected' : '' }}>Izin</option>
                    <option value="sakit" {{ request('status') == 'sakit' ? 'selected' : '' }}>Sakit</option>
                    <option value="alpha" {{ request('status') == 'alpha' ? 'selected' : '' }}>Alpha</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Filter
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Attendance Table -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Tanggal</th>
                        <th>Kelas</th>
                        <th>NIS</th>
                        <th>Nama Siswa</th>
                        <th>Status</th>
                        <th>Keterangan</th>
                        <th>Dicatat Oleh</th>
                        <th>Waktu Input</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendances as $attendance)
                    <tr>
                        <td>{{ $attendance->date->format('d/m/Y') }}</td>
                        <td>
                            <span class="badge bg-secondary">{{ $attendance->class->name }}</span>
                        </td>
                        <td>{{ $attendance->student->nis }}</td>
                        <td>{{ $attendance->student->name }}</td>
                        <td>
                            <span class="badge bg-{{ $attendance->status_color }}">
                                {{ ucfirst($attendance->status) }}
                            </span>
                        </td>
                        <td>{{ $attendance->notes ?: '-' }}</td>
                        <td>{{ $attendance->recordedBy->name }}</td>
                        <td>{{ $attendance->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center">Tidak ada data absensi</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($attendances->hasPages())
        <div class="d-flex justify-content-center mt-3">
            {{ $attendances->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>
@endsection