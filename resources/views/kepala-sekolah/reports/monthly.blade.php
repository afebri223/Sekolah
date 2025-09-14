<!-- resources/views/kepala-sekolah/reports/monthly.blade.php -->
@extends('layouts.app')

@section('title', 'Laporan Bulanan - ' . \Carbon\Carbon::create($year, $month, 1)->format('F Y'))

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2><i class="fas fa-calendar-alt"></i> Laporan Absensi Bulanan</h2>
                <p class="text-muted">{{ \Carbon\Carbon::create($year, $month, 1)->format('F Y') }} - Analisis komprehensif kehadiran siswa</p>
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
        <h6 class="mb-0"><i class="fas fa-filter"></i> Filter & Periode</h6>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Bulan/Tahun</label>
                <input type="month" name="month" class="form-control" value="{{ $monthYear }}" max="{{ now()->format('Y-m') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Filter Kelas</label>
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
                <label class="form-label">Min. Kehadiran</label>
                <select name="min_attendance" class="form-select">
                    <option value="0">Semua</option>
                    <option value="50" {{ request('min_attendance') == '50' ? 'selected' : '' }}>≥ 50%</option>
                    <option value="70" {{ request('min_attendance') == '70' ? 'selected' : '' }}>≥ 70%</option>
                    <option value="85" {{ request('min_attendance') == '85' ? 'selected' : '' }}>≥ 85%</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search"></i> Filter
                </button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('kepala-sekolah.reports.yearly', ['year' => $year]) }}" class="btn btn-info w-100">
                    <i class="fas fa-calendar"></i> Tahunan
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Executive Summary Dashboard -->
<div class="row mb-4">
    <div class="col-md-2">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <i class="fas fa-school fa-2x mb-2"></i>
                <h4>{{ $summaryByClass->count() }}</h4>
                <p class="mb-0">Kelas Aktif</p>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card bg-secondary text-white">
            <div class="card-body text-center">
                <i class="fas fa-users fa-2x mb-2"></i>
                <h4>{{ $summaryByClass->sum(function($class) { return count($class); }) }}</h4>
                <p class="mb-0">Total Siswa</p>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <i class="fas fa-check-circle fa-2x mb-2"></i>
                <h4>{{ $summaryByClass->sum(function($class) { return collect($class)->sum('hadir'); }) }}</h4>
                <p class="mb-0">Total Hadir</p>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card bg-warning text-white">
            <div class="card-body text-center">
                <i class="fas fa-hand-paper fa-2x mb-2"></i>
                <h4>{{ $summaryByClass->sum(function($class) { return collect($class)->sum('izin'); }) }}</h4>
                <p class="mb-0">Total Izin</p>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <i class="fas fa-thermometer-half fa-2x mb-2"></i>
                <h4>{{ $summaryByClass->sum(function($class) { return collect($class)->sum('sakit'); }) }}</h4>
                <p class="mb-0">Total Sakit</p>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card bg-danger text-white">
            <div class="card-body text-center">
                <i class="fas fa-times-circle fa-2x mb-2"></i>
                <h4>{{ $summaryByClass->sum(function($class) { return collect($class)->sum('alpha'); }) }}</h4>
                <p class="mb-0">Total Alpha</p>
            </div>
        </div>
    </div>
</div>

<!-- Performance Analytics -->
<div class="row mb-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-chart-bar"></i> Analisis Performa Kelas</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Ranking</th>
                                <th>Kelas</th>
                                <th class="text-center">Siswa</th>
                                <th class="text-center">Hadir</th>
                                <th class="text-center">Izin</th>
                                <th class="text-center">Sakit</th>
                                <th class="text-center">Alpha</th>
                                <th class="text-center">Kehadiran</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($summaryByClass->sortByDesc(function($students) { 
                                $totalStudents = count($students);
                                $totalHadir = collect($students)->sum('hadir');
                                return $totalStudents > 0 ? ($totalHadir / ($totalStudents * 30)) * 100 : 0; // Assuming 30 days per month
                            }) as $className => $students)
                                @php
                                    $totalStudents = count($students);
                                    $totalHadir = collect($students)->sum('hadir');
                                    $totalIzin = collect($students)->sum('izin');
                                    $totalSakit = collect($students)->sum('sakit');
                                    $totalAlpha = collect($students)->sum('alpha');
                                    $totalDays = collect($students)->sum('total_days');
                                    $attendanceRate = $totalDays > 0 ? round(($totalHadir / $totalDays) * 100, 1) : 0;
                                @endphp
                                <tr>
                                    <td class="text-center">
                                        @if($loop->iteration == 1)
                                            <span class="badge bg-warning"><i class="fas fa-trophy"></i> 1</span>
                                        @elseif($loop->iteration == 2)
                                            <span class="badge bg-secondary"><i class="fas fa-medal"></i> 2</span>
                                        @elseif($loop->iteration == 3)
                                            <span class="badge bg-dark"><i class="fas fa-award"></i> 3</span>
                                        @else
                                            <span class="badge bg-light text-dark">{{ $loop->iteration }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ $className }}</strong>
                                        @if($loop->iteration <= 3)
                                            <i class="fas fa-star text-warning ms-1"></i>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-primary">{{ $totalStudents }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="text-success fw-bold">{{ $totalHadir }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="text-warning fw-bold">{{ $totalIzin }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="text-info fw-bold">{{ $totalSakit }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="text-danger fw-bold">{{ $totalAlpha }}</span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex flex-column align-items-center">
                                            <strong class="text-{{ $attendanceRate >= 85 ? 'success' : ($attendanceRate >= 70 ? 'warning' : 'danger') }}">
                                                {{ $attendanceRate }}%
                                            </strong>
                                            <div class="progress mt-1" style="width: 80px; height: 4px;">
                                                <div class="progress-bar bg-{{ $attendanceRate >= 85 ? 'success' : ($attendanceRate >= 70 ? 'warning' : 'danger') }}" 
                                                     style="width: {{ $attendanceRate }}%"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        @if($attendanceRate >= 90)
                                            <span class="badge bg-success"><i class="fas fa-star"></i> Excellent</span>
                                        @elseif($attendanceRate >= 85)
                                            <span class="badge bg-primary"><i class="fas fa-thumbs-up"></i> Very Good</span>
                                        @elseif($attendanceRate >= 75)
                                            <span class="badge bg-warning"><i class="fas fa-exclamation-triangle"></i> Good</span>
                                        @elseif($attendanceRate >= 60)
                                            <span class="badge bg-orange"><i class="fas fa-exclamation"></i> Fair</span>
                                        @else
                                            <span class="badge bg-danger"><i class="fas fa-times-circle"></i> Poor</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <!-- Top Performers -->
        <div class="card mb-3">
            <div class="card-header bg-success text-white">
                <h6 class="mb-0"><i class="fas fa-trophy"></i> Kelas Terbaik</h6>
            </div>
            <div class="card-body">
                @php
                    $topClasses = $summaryByClass->map(function($students, $className) {
                        $totalStudents = count($students);
                        $totalHadir = collect($students)->sum('hadir');
                        $totalDays = collect($students)->sum('total_days');
                        return [
                            'name' => $className,
                            'students' => $totalStudents,
                            'rate' => $totalDays > 0 ? round(($totalHadir / $totalDays) * 100, 1) : 0
                        ];
                    })->sortByDesc('rate')->take(3);
                @endphp
                
                @foreach($topClasses as $index => $classData)
                    <div class="d-flex justify-content-between align-items-center {{ !$loop->last ? 'mb-3 pb-3 border-bottom' : '' }}">
                        <div class="d-flex align-items-center">
                            @if($index == 0)
                                <i class="fas fa-trophy text-warning fa-lg me-2"></i>
                            @elseif($index == 1)
                                <i class="fas fa-medal text-secondary fa-lg me-2"></i>
                            @else
                                <i class="fas fa-award text-dark fa-lg me-2"></i>
                            @endif
                            <div>
                                <strong>{{ $classData['name'] }}</strong>
                                <br><small class="text-muted">{{ $classData['students'] }} siswa</small>
                            </div>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-success fs-6">{{ $classData['rate'] }}%</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        
        <!-- Need Attention -->
        <div class="card">
            <div class="card-header bg-warning text-white">
                <h6 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Perlu Perhatian</h6>
            </div>
            <div class="card-body">
                @php
                    $needAttention = $summaryByClass->map(function($students, $className) {
                        $totalStudents = count($students);
                        $totalHadir = collect($students)->sum('hadir');
                        $totalAlpha = collect($students)->sum('alpha');
                        $totalDays = collect($students)->sum('total_days');
                        $rate = $totalDays > 0 ? round(($totalHadir / $totalDays) * 100, 1) : 0;
                        return [
                            'name' => $className,
                            'students' => $totalStudents,
                            'alpha' => $totalAlpha,
                            'rate' => $rate
                        ];
                    })->where('rate', '<', 75)->sortBy('rate');
                @endphp
                
                @if($needAttention->count() > 0)
                    @foreach($needAttention as $classData)
                        <div class="d-flex justify-content-between align-items-center {{ !$loop->last ? 'mb-3 pb-3 border-bottom' : '' }}">
                            <div>
                                <strong>{{ $classData['name'] }}</strong>
                                <br><small class="text-muted">{{ $classData['alpha'] }} siswa alpha</small>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-{{ $classData['rate'] >= 60 ? 'warning' : 'danger' }} fs-6">{{ $classData['rate'] }}%</span>
                            </div>
                        </div>
                    @endforeach
                    
                    <div class="mt-3">
                        <div class="alert alert-warning">
                            <i class="fas fa-lightbulb"></i>
                            <strong>Rekomendasi:</strong> Lakukan pendekatan khusus ke kelas dengan tingkat kehadiran rendah.
                        </div>
                    </div>
                @else
                    <div class="text-center text-muted py-3">
                        <i class="fas fa-check-circle fa-2x mb-2 text-success"></i>
                        <p class="mb-0">Semua kelas memiliki tingkat kehadiran yang baik!</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Detailed Student Summary (if single class selected) -->
@if($classId !== 'all' && $summaryByClass->count() == 1)
    <div class="card">
        <div class="card-header">
            <h6 class="mb-0"><i class="fas fa-users"></i> Detail Siswa Kelas {{ $summaryByClass->keys()->first() }}</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>NIS</th>
                            <th>Nama Siswa</th>
                            <th class="text-center">Hadir</th>
                            <th class="text-center">Izin</th>
                            <th class="text-center">Sakit</th>
                            <th class="text-center">Alpha</th>
                            <th class="text-center">Total Hari</th>
                            <th class="text-center">Kehadiran</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($summaryByClass->first() as $index => $studentData)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td><small class="text-muted">{{ $studentData['student']->nis }}</small></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-2">
                                            <span class="avatar-title bg-primary text-white rounded-circle">
                                                {{ strtoupper(substr($studentData['student']->name, 0, 1)) }}
                                            </span>
                                        </div>
                                        <strong>{{ $studentData['student']->name }}</strong>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="text-success fw-bold">{{ $studentData['hadir'] }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="text-warning fw-bold">{{ $studentData['izin'] }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="text-info fw-bold">{{ $studentData['sakit'] }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="text-danger fw-bold">{{ $studentData['alpha'] }}</span>
                                </td>
                                <td class="text-center">{{ $studentData['total_days'] }}</td>
                                <td class="text-center">
                                    <strong class="text-{{ $studentData['percentage'] >= 85 ? 'success' : ($studentData['percentage'] >= 70 ? 'warning' : 'danger') }}">
                                        {{ $studentData['percentage'] }}%
                                    </strong>
                                </td>
                                <td class="text-center">
                                    @if($studentData['percentage'] >= 90)
                                        <span class="badge bg-success">Excellent</span>
                                    @elseif($studentData['percentage'] >= 80)
                                        <span class="badge bg-primary">Good</span>
                                    @elseif($studentData['percentage'] >= 70)
                                        <span class="badge bg-warning">Fair</span>
                                    @else
                                        <span class="badge bg-danger">Poor</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endif

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form when inputs change
    document.querySelector('input[name="month"]').addEventListener('change', function() {
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
    typeInput.value = 'monthly';
    form.appendChild(typeInput);
    
    const formatInput = document.createElement('input');
    formatInput.type = 'hidden';
    formatInput.name = 'format';
    formatInput.value = format;
    form.appendChild(formatInput);
    
    // Add current filters
    const monthInput = document.createElement('input');
    monthInput.type = 'hidden';
    monthInput.name = 'month';
    monthInput.value = '{{ $monthYear }}';
    form.appendChild(monthInput);
    
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
}
</style>
@endpush
@endsection