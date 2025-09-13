<!-- resources/views/attendance/history.blade.php -->
@extends('layouts.app')

@section('title', 'Riwayat Absensi')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2><i class="fas fa-history"></i> Riwayat Absensi Kelas {{ $class->name }}</h2>
                <p class="text-muted">Riwayat absensi siswa dalam kelas yang Anda ampu</p>
            </div>
            <div>
                <a href="{{ route('wali-kelas.dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                <a href="{{ route('attendance.index') }}" class="btn btn-primary">
                    <i class="fas fa-calendar-plus"></i> Input Absensi
                </a>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h5 class="mb-0"><i class="fas fa-table"></i> Data Absensi</h5>
            </div>
            <div class="col-md-6 text-end">
                <form method="GET" class="d-inline-flex">
                    <input type="date" name="date" class="form-control form-control-sm me-2" 
                           value="{{ request('date') }}">
                    <select name="status" class="form-select form-select-sm me-2">
                        <option value="">Semua Status</option>
                        <option value="hadir" {{ request('status') == 'hadir' ? 'selected' : '' }}>Hadir</option>
                        <option value="izin" {{ request('status') == 'izin' ? 'selected' : '' }}>Izin</option>
                        <option value="sakit" {{ request('status') == 'sakit' ? 'selected' : '' }}>Sakit</option>
                        <option value="alpha" {{ request('status') == 'alpha' ? 'selected' : '' }}>Alpha</option>
                    </select>
                    <button type="submit" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Tanggal</th>
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
                        <td colspan="7" class="text-center">Tidak ada data absensi</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($attendances->hasPages())
        <div class="d-flex justify-content-center mt-3">
            {{ $attendances->links() }}
        </div>
        @endif
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <h4>{{ $attendances->where('status', 'hadir')->count() }}</h4>
                <p class="mb-0">Total Hadir</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body text-center">
                <h4>{{ $attendances->where('status', 'izin')->count() }}</h4>
                <p class="mb-0">Total Izin</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <h4>{{ $attendances->where('status', 'sakit')->count() }}</h4>
                <p class="mb-0">Total Sakit</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-danger text-white">
            <div class="card-body text-center">
                <h4>{{ $attendances->where('status', 'alpha')->count() }}</h4>
                <p class="mb-0">Total Alpha</p>
            </div>
        </div>
    </div>
</div>
@endsection