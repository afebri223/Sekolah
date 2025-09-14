<!-- resources/views/guru/reports/daily.blade.php -->
@extends('layouts.app')

@section('title', 'Laporan Harian - ' . $date->format('d F Y'))

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2><i class="fas fa-calendar-day"></i> Laporan Absensi Harian</h2>
                <p class="text-muted">{{ $date->format('l, d F Y') }}</p>
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
                <label class="form-label">Tanggal</label>
                <input type="date" name="date" class="form-control" value="{{ $date->format('Y-m-d') }}" max="{{ today()->format('Y-m-d') }}">
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
                <a href="{{ route('guru.reports.monthly', ['month' => $date->format('Y-m')]) }}" class="btn btn-success w-100">
                    <i class="fas fa-calendar-alt"></i> Bulanan
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Summary Statistics -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <i class="fas fa-clipboard-list fa-2x mb-2"></i>
                <h4>{{ $stats['total_recorded'] }}</h4>
                <p class="mb-0">Total Absensi</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <i class="fas fa-check-circle fa-2x mb-2"></i>
                <h4>{{ $stats['hadir'] }}</h4>
                <p class="mb-0">Hadir</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body text-center">
                <i class="fas fa-exclamation-circle fa-2x mb-2"></i>
                <h4>{{ $stats['izin'] + $stats['sakit'] }}</h4>
                <p class="mb-0">Izin + Sakit</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-danger text-white">
            <div class="card-body text-center">
                <i class="fas fa-times-circle fa-2x mb-2"></i>
                <h4>{{ $stats['alpha'] }}</h4>
                <p class="mb-0">Alpha</p>
            </div>
        </div>
    </div>
</div>

<!-- Attendance Rate -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1">Tingkat Kehadiran</h5>
                        <p class="text-muted mb-0">Persentase siswa yang hadir hari ini</p>
                    </div>
                    <div class="text-end">
                        <h3 class="text-{{ $stats['attendance_rate'] >= 80 ? 'success' : ($stats['attendance_rate'] >= 60 ? 'warning' : 'danger') }} mb-1">
                            {{ $stats['attendance_rate'] }}%
                        </h3>
                        <small class="text-muted">{{ $stats['hadir'] }}/{{ $stats['total_recorded'] }}</small>
                    </div>
                </div>
                <div class="progress mt-3" style="height: 10px;">
                    <div class="progress-bar bg-{{ $stats['attendance_rate'] >= 80 ? 'success' : ($stats['attendance_rate'] >= 60 ? 'warning' : 'danger') }}" 
                         style="width: {{ $stats['attendance_rate'] }}%"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Attendance by Class -->
@if($attendancesByClass->count() > 0)
    @foreach($attendancesByClass as $className => $classAttendances)
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">
                    <i class="fas fa-door-open"></i> {{ $className }}
                    <span class="badge bg-secondary ms-2">{{ $classAttendances->count() }} siswa</span>
                </h6>
                <div>
                    @php
                        $classStats = [
                            'hadir' => $classAttendances->where('status', 'hadir')->count(),
                            'total' => $classAttendances->count()
                        ];
                        $classRate = $classStats['total'] > 0 ? round(($classStats['hadir'] / $classStats['total']) * 100, 1) : 0;
                    @endphp
                    <span class="badge bg-{{ $classRate >= 80 ? 'success' : ($classRate >= 60 ? 'warning' : 'danger') }}">
                        {{ $classRate }}% Kehadiran
                    </span>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-sm">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">No</th>
                                <th width="15%">NIS</th>
                                <th width="30%">Nama Siswa</th>
                                <th width="15%">Status</th>
                                <th width="25%">Keterangan</th>
                                <th width="10%">Waktu Input</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($classAttendances->sortBy('student.name') as $index => $attendance)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><small class="text-muted">{{ $attendance->student->nis }}</small></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-2">
                                                <span class="avatar-title bg-primary text-white rounded-circle">
                                                    {{ strtoupper(substr($attendance->student->name, 0, 1)) }}
                                                </span>
                                            </div>
                                            <div>
                                                <strong>{{ $attendance->student->name }}</strong>
                                                <br><small class="text-muted">{{ $attendance->student->gender_full }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $statusConfig = [
                                                'hadir' => ['bg-success', 'check-circle'],
                                                'izin' => ['bg-warning', 'hand-paper'],
                                                'sakit' => ['bg-info', 'thermometer-half'],
                                                'alpha' => ['bg-danger', 'times-circle']
                                            ];
                                            $config = $statusConfig[$attendance->status] ?? ['bg-secondary', 'question'];
                                        @endphp
                                        <span class="badge {{ $config[0] }}">
                                            <i class="fas fa-{{ $config[1] }}"></i> {{ ucfirst($attendance->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($attendance->notes)
                                            <small class="text-muted">{{ Str::limit($attendance->notes, 40) }}</small>
                                        @else
                                            <small class="text-muted fst-italic">-</small>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $attendance->created_at->format('H:i') }}</small>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Class Summary -->
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="d-flex justify-content-around text-center bg-light rounded p-2">
                            <div>
                                <strong class="text-success">{{ $classAttendances->where('status', 'hadir')->count() }}</strong>
                                <br><small class="text-muted">Hadir</small>
                            </div>
                            <div>
                                <strong class="text-warning">{{ $classAttendances->where('status', 'izin')->count() }}</strong>
                                <br><small class="text-muted">Izin</small>
                            </div>
                            <div>
                                <strong class="text-info">{{ $classAttendances->where('status', 'sakit')->count() }}</strong>
                                <br><small class="text-muted">Sakit</small>
                            </div>
                            <div>
                                <strong class="text-danger">{{ $classAttendances->where('status', 'alpha')->count() }}</strong>
                                <br><small class="text-muted">Alpha</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@else
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">Tidak ada data absensi</h5>
            <p class="text-muted">Tidak ada absensi yang tercatat untuk tanggal dan filter yang dipilih.</p>
            <a href="{{ route('guru.reports.index') }}" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i> Kembali ke Halaman Utama
            </a>
        </div>
    </div>
@endif

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form when date changes
    document.querySelector('input[name="date"]').addEventListener('change', function() {
        this.form.submit();
    });
    
    // Auto-submit form when class changes
    document.querySelector('select[name="class_id"]').addEventListener('change', function() {
        this.form.submit();
    });
    
    // Print functionality
    function printReport() {
        window.print();
    }
    
    // Add print button to header
    const headerActions = document.querySelector('.d-flex .btn-secondary').parentNode;
    const printBtn = document.createElement('a');
    printBtn.className = 'btn btn-info me-2';
    printBtn.innerHTML = '<i class="fas fa-print"></i> Cetak';
    printBtn.onclick = printReport;
    headerActions.insertBefore(printBtn, headerActions.firstChild);
});
</script>

<style>
@media print {
    .card-header { background-color: #f8f9fa !important; }
    .btn, .form-control, .form-select { display: none; }
    .card { border: 1px solid #000 !important; }
}
</style>
@endpush
@endsection