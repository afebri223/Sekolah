<!-- resources/views/kepala-sekolah/reports/daily.blade.php -->
@extends('layouts.app')

@section('title', 'Laporan Harian - ' . $date->format('d F Y'))

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2><i class="fas fa-calendar-day"></i> Laporan Absensi Harian</h2>
                <p class="text-muted">{{ $date->format('l, d F Y') }} - Monitoring kehadiran seluruh siswa</p>
            </div>
            <div>
                <button type="button" class="btn btn-success me-2" onclick="exportReport('excel')">
                    <i class="fas fa-file-excel"></i> Export Excel
                </button>
                <button type="button" class="btn btn-danger me-2" onclick="exportReport('pdf')">
                    <i class="fas fa-file-pdf"></i> Export PDF
                </button>
                <a href="{{ route('kepala-sekolah.reports.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Advanced Filter Section -->
<div class="card mb-4">
    <div class="card-header">
        <h6 class="mb-0"><i class="fas fa-filter"></i> Filter & Analisis</h6>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Tanggal</label>
                <input type="date" name="date" class="form-control" value="{{ $date->format('Y-m-d') }}" max="{{ today()->format('Y-m-d') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Filter Kelas</label>
                <select name="class_id" class="form-select">
                    <option value="all" {{ $classId == 'all' ? 'selected' : '' }}>Semua Kelas</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ $classId == $class->id ? 'selected' : '' }}>
                            {{ $class->name }} ({{ $class->grade }} - {{ $class->students()->where('status', 'active')->count() }} siswa)
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select name="status_filter" class="form-select">
                    <option value="all">Semua Status</option>
                    <option value="hadir" {{ request('status_filter') == 'hadir' ? 'selected' : '' }}>Hadir</option>
                    <option value="izin" {{ request('status_filter') == 'izin' ? 'selected' : '' }}>Izin</option>
                    <option value="sakit" {{ request('status_filter') == 'sakit' ? 'selected' : '' }}>Sakit</option>
                    <option value="alpha" {{ request('status_filter') == 'alpha' ? 'selected' : '' }}>Alpha</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Urutan</label>
                <select name="sort_by" class="form-select">
                    <option value="class">Per Kelas</option>
                    <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Nama A-Z</option>
                    <option value="status" {{ request('sort_by') == 'status' ? 'selected' : '' }}>Status</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search"></i> Filter
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Executive Summary -->
<div class="row mb-4">
    <div class="col-md-2">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <i class="fas fa-users fa-2x mb-2"></i>
                <h4>{{ $stats['total_students'] }}</h4>
                <p class="mb-0">Total Siswa</p>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <i class="fas fa-check-circle fa-2x mb-2"></i>
                <h4>{{ $stats['total_hadir'] }}</h4>
                <p class="mb-0">Hadir</p>
                <small>{{ $stats['total_students'] > 0 ? round(($stats['total_hadir'] / $stats['total_students']) * 100, 1) : 0 }}%</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card bg-warning text-white">
            <div class="card-body text-center">
                <i class="fas fa-hand-paper fa-2x mb-2"></i>
                <h4>{{ $stats['total_izin'] }}</h4>
                <p class="mb-0">Izin</p>
                <small>{{ $stats['total_students'] > 0 ? round(($stats['total_izin'] / $stats['total_students']) * 100, 1) : 0 }}%</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <i class="fas fa-thermometer-half fa-2x mb-2"></i>
                <h4>{{ $stats['total_sakit'] }}</h4>
                <p class="mb-0">Sakit</p>
                <small>{{ $stats['total_students'] > 0 ? round(($stats['total_sakit'] / $stats['total_students']) * 100, 1) : 0 }}%</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card bg-danger text-white">
            <div class="card-body text-center">
                <i class="fas fa-times-circle fa-2x mb-2"></i>
                <h4>{{ $stats['total_alpha'] }}</h4>
                <p class="mb-0">Alpha</p>
                <small>{{ $stats['total_students'] > 0 ? round(($stats['total_alpha'] / $stats['total_students']) * 100, 1) : 0 }}%</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card bg-dark text-white">
            <div class="card-body text-center">
                <i class="fas fa-percentage fa-2x mb-2"></i>
                <h4>{{ $stats['total_students'] > 0 ? round(($stats['total_hadir'] / $stats['total_students']) * 100, 1) : 0 }}%</h4>
                <p class="mb-0">Kehadiran</p>
                <small>Tingkat kehadiran</small>
            </div>
        </div>
    </div>
</div>

<!-- Attendance Rate Progress -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="mb-1">Tingkat Kehadiran Keseluruhan</h5>
                        <p class="text-muted mb-0">Target minimal: 85% | Kondisi baik: 90%+</p>
                    </div>
                    <div class="text-end">
                        @php
                            $attendanceRate = $stats['total_students'] > 0 ? round(($stats['total_hadir'] / $stats['total_students']) * 100, 1) : 0;
                        @endphp
                        <h3 class="text-{{ $attendanceRate >= 85 ? 'success' : ($attendanceRate >= 70 ? 'warning' : 'danger') }} mb-1">
                            {{ $attendanceRate }}%
                        </h3>
                        <small class="text-muted">{{ $stats['total_hadir'] }}/{{ $stats['total_students'] }}</small>
                    </div>
                </div>
                <div class="progress" style="height: 15px;">
                    <div class="progress-bar bg-{{ $attendanceRate >= 85 ? 'success' : ($attendanceRate >= 70 ? 'warning' : 'danger') }}" 
                         style="width: {{ $attendanceRate }}%">
                        {{ $attendanceRate }}%
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col text-center">
                        <small class="text-muted">0%</small>
                    </div>
                    <div class="col text-center">
                        <small class="text-muted">Target: 85%</small>
                    </div>
                    <div class="col text-center">
                        <small class="text-muted">Ideal: 90%</small>
                    </div>
                    <div class="col text-center">
                        <small class="text-muted">100%</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Detailed Attendance by Class -->
@if($attendances->count() > 0)
    @foreach($attendances as $className => $classAttendances)
        @php
            $classStats = [
                'total' => $classAttendances->count(),
                'hadir' => $classAttendances->where('status', 'hadir')->count(),
                'izin' => $classAttendances->where('status', 'izin')->count(),
                'sakit' => $classAttendances->where('status', 'sakit')->count(),
                'alpha' => $classAttendances->where('status', 'alpha')->count(),
            ];
            $classRate = $classStats['total'] > 0 ? round(($classStats['hadir'] / $classStats['total']) * 100, 1) : 0;
        @endphp
        
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-0">
                        <i class="fas fa-door-open"></i> {{ $className }}
                        <span class="badge bg-secondary ms-2">{{ $classStats['total'] }} siswa</span>
                    </h6>
                </div>
                <div>
                    <span class="badge bg-{{ $classRate >= 85 ? 'success' : ($classRate >= 70 ? 'warning' : 'danger') }} fs-6">
                        {{ $classRate }}% Kehadiran
                    </span>
                    <button type="button" class="btn btn-sm btn-outline-primary ms-2" data-bs-toggle="collapse" data-bs-target="#class{{ $loop->index }}">
                        <i class="fas fa-eye"></i> Detail
                    </button>
                </div>
            </div>
            
            <!-- Class Summary Stats -->
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3">
                        <div class="border rounded p-2">
                            <h5 class="text-success mb-1">{{ $classStats['hadir'] }}</h5>
                            <small class="text-muted">Hadir</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border rounded p-2">
                            <h5 class="text-warning mb-1">{{ $classStats['izin'] }}</h5>
                            <small class="text-muted">Izin</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border rounded p-2">
                            <h5 class="text-info mb-1">{{ $classStats['sakit'] }}</h5>
                            <small class="text-muted">Sakit</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border rounded p-2">
                            <h5 class="text-danger mb-1">{{ $classStats['alpha'] }}</h5>
                            <small class="text-muted">Alpha</small>
                        </div>
                    </div>
                </div>
                
                <!-- Progress Bar -->
                <div class="mt-3">
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-success" style="width: {{ $classStats['total'] > 0 ? ($classStats['hadir'] / $classStats['total']) * 100 : 0 }}%"></div>
                        <div class="progress-bar bg-warning" style="width: {{ $classStats['total'] > 0 ? ($classStats['izin'] / $classStats['total']) * 100 : 0 }}%"></div>
                        <div class="progress-bar bg-info" style="width: {{ $classStats['total'] > 0 ? ($classStats['sakit'] / $classStats['total']) * 100 : 0 }}%"></div>
                        <div class="progress-bar bg-danger" style="width: {{ $classStats['total'] > 0 ? ($classStats['alpha'] / $classStats['total']) * 100 : 0 }}%"></div>
                    </div>
                </div>
            </div>
            
            <!-- Detailed Student List (Collapsible) -->
            <div class="collapse" id="class{{ $loop->index }}">
                <div class="card-body border-top">
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
                                            <br><small class="text-muted">{{ $attendance->recordedBy->name }}</small>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
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
            <p class="text-muted">Belum ada absensi yang tercatat untuk tanggal dan filter yang dipilih.</p>
            <a href="{{ route('kepala-sekolah.reports.index') }}" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i> Kembali ke Pusat Laporan
            </a>
        </div>
    </div>
@endif

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form when inputs change
    document.querySelector('input[name="date"]').addEventListener('change', function() {
        this.form.submit();
    });
    
    document.querySelector('select[name="class_id"]').addEventListener('change', function() {
        this.form.submit();
    });
    
    // Print functionality
    function printReport() {
        window.print();
    }
    
    // Add print button to header
    const headerActions = document.querySelector('.d-flex .btn-secondary').parentNode;
    const printBtn = document.createElement('button');
    printBtn.className = 'btn btn-info me-2';
    printBtn.innerHTML = '<i class="fas fa-print"></i> Cetak';
    printBtn.onclick = printReport;
    headerActions.insertBefore(printBtn, headerActions.querySelector('.btn-secondary'));
});

function exportReport(format) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("kepala-sekolah.reports.export") }}';
    
    // Add CSRF token
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    form.appendChild(csrfInput);
    
    // Add form data
    const typeInput = document.createElement('input');
    typeInput.type = 'hidden';
    typeInput.name = 'type';
    typeInput.value = 'daily';
    form.appendChild(typeInput);
    
    const formatInput = document.createElement('input');
    formatInput.type = 'hidden';
    formatInput.name = 'format';
    formatInput.value = format;
    form.appendChild(formatInput);
    
    // Add current filters
    const dateInput = document.createElement('input');
    dateInput.type = 'hidden';
    dateInput.name = 'date';
    dateInput.value = '{{ $date->format("Y-m-d") }}';
    form.appendChild(dateInput);
    
    const classInput = document.createElement('input');
    classInput.type = 'hidden';
    classInput.name = 'class_id';
    classInput.value = '{{ $classId }}';
    form.appendChild(classInput);
    
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}
</script>

<style>
@media print {
    .btn, .form-control, .form-select { display: none !important; }
    .card { border: 1px solid #000 !important; }
    .collapse { display: block !important; }
}
</style>
@endpush
@endsection