<!-- resources/views/wali-kelas/attendance/history.blade.php -->
@extends('layouts.app')

@section('title', 'Riwayat Absensi - ' . $class->name)

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2><i class="fas fa-history"></i> Riwayat Absensi Kelas {{ $class->name }}</h2>
                <p class="text-muted">{{ $class->grade }} {{ $class->major }} - Riwayat lengkap absensi siswa</p>
            </div>
            <div>
                <a href="{{ route('wali-kelas.attendance.index') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Input Absensi
                </a>
                <a href="{{ route('wali-kelas.dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Dashboard
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="card mb-4">
    <div class="card-header">
        <h6 class="mb-0"><i class="fas fa-filter"></i> Filter Riwayat</h6>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Bulan/Tahun</label>
                <input type="month" name="month" class="form-control" value="{{ $month }}" max="{{ now()->format('Y-m') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Siswa</label>
                <select name="student_id" class="form-select">
                    <option value="">Semua Siswa</option>
                    @foreach($students as $student)
                        <option value="{{ $student->id }}" {{ $studentId == $student->id ? 'selected' : '' }}>
                            {{ $student->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="hadir" {{ request('status') == 'hadir' ? 'selected' : '' }}>Hadir</option>
                    <option value="izin" {{ request('status') == 'izin' ? 'selected' : '' }}>Izin</option>
                    <option value="sakit" {{ request('status') == 'sakit' ? 'selected' : '' }}>Sakit</option>
                    <option value="alpha" {{ request('status') == 'alpha' ? 'selected' : '' }}>Alpha</option>
                </select>
            </div>
            <div class="col-md-3">
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

<!-- Monthly Summary -->
@if($monthlySummary->count() > 0)
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-chart-pie"></i> Ringkasan Bulan {{ \Carbon\Carbon::parse($month)->format('F Y') }}</h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3">
                        <div class="border rounded p-3">
                            <h4 class="text-success mb-1">{{ $monthlySummary->get('hadir')->count ?? 0 }}</h4>
                            <small class="text-muted">Total Hadir</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border rounded p-3">
                            <h4 class="text-warning mb-1">{{ $monthlySummary->get('izin')->count ?? 0 }}</h4>
                            <small class="text-muted">Total Izin</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border rounded p-3">
                            <h4 class="text-info mb-1">{{ $monthlySummary->get('sakit')->count ?? 0 }}</h4>
                            <small class="text-muted">Total Sakit</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border rounded p-3">
                            <h4 class="text-danger mb-1">{{ $monthlySummary->get('alpha')->count ?? 0 }}</h4>
                            <small class="text-muted">Total Alpha</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Attendance Records -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0"><i class="fas fa-list"></i> Data Absensi</h6>
        <div>
            <small class="text-muted">{{ $attendances->total() }} record ditemukan</small>
        </div>
    </div>
    <div class="card-body">
        @if($attendances->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Tanggal</th>
                            <th>Siswa</th>
                            <th>Status</th>
                            <th>Keterangan</th>
                            <th>Dicatat Oleh</th>
                            <th>Waktu Input</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($attendances as $attendance)
                            <tr>
                                <td>
                                    <strong>{{ $attendance->date->format('d M Y') }}</strong>
                                    <br><small class="text-muted">{{ $attendance->date->format('l') }}</small>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-2">
                                            <span class="avatar-title bg-primary text-white rounded-circle">
                                                {{ strtoupper(substr($attendance->student->name, 0, 1)) }}
                                            </span>
                                        </div>
                                        <div>
                                            <strong>{{ $attendance->student->name }}</strong>
                                            <br><small class="text-muted">{{ $attendance->student->nis }}</small>
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
                                        <span class="text-muted">{{ Str::limit($attendance->notes, 50) }}</span>
                                    @else
                                        <span class="text-muted fst-italic">-</span>
                                    @endif
                                </td>
                                <td>
                                    <small>{{ $attendance->recordedBy->name }}</small>
                                </td>
                                <td>
                                    <small class="text-muted">{{ $attendance->created_at->format('d M Y H:i') }}</small>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-center">
                {{ $attendances->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Tidak ada data absensi</h5>
                <p class="text-muted">Belum ada absensi yang tercatat untuk filter yang dipilih.</p>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form when month changes
    document.querySelector('input[name="month"]').addEventListener('change', function() {
        this.form.submit();
    });
    
    // Auto-submit form when student changes
    document.querySelector('select[name="student_id"]').addEventListener('change', function() {
        this.form.submit();
    });
});
</script>
@endpush
@endsection