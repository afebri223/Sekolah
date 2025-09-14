<!-- resources/views/kepala-sekolah/reports/yearly.blade.php -->
@extends('layouts.app')

@section('title', 'Laporan Tahunan - ' . $year)

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2><i class="fas fa-calendar"></i> Laporan Absensi Tahunan {{ $year }}</h2>
                <p class="text-muted">Analisis tren dan performa jangka panjang seluruh kelas</p>
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

<!-- Filter Section -->
<div class="card mb-4">
    <div class="card-header">
        <h6 class="mb-0"><i class="fas fa-filter"></i> Periode & Filter</h6>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Tahun</label>
                <select name="year" class="form-select">
                    @for($y = now()->year; $y >= now()->year - 5; $y--)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
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
                <label class="form-label">View</label>
                <select name="view" class="form-select">
                    <option value="summary" {{ request('view', 'summary') == 'summary' ? 'selected' : '' }}>Summary</option>
                    <option value="detailed" {{ request('view') == 'detailed' ? 'selected' : '' }}>Detailed</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search"></i> Filter
                </button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('kepala-sekolah.reports.monthly', ['month' => now()->format('Y-m')]) }}" class="btn btn-success w-100">
                    <i class="fas fa-calendar-alt"></i> Bulanan
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Annual Statistics Overview -->
<div class="row mb-4">
    <div class="col-md-2">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <i class="fas fa-calendar-alt fa-2x mb-2"></i>
                <h4>12</h4>
                <p class="mb-0">Bulan</p>
                <small>Data lengkap</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <i class="fas fa-chart-line fa-2x mb-2"></i>
                @php
                    $totalRecords = collect($monthlyStats)->sum('total');
                    $totalHadir = collect($monthlyStats)->sum('hadir');
                    $yearlyRate = $totalRecords > 0 ? round(($totalHadir / $totalRecords) * 100, 1) : 0;
                @endphp
                <h4>{{ $yearlyRate }}%</h4>
                <p class="mb-0">Rata-rata</p>
                <small>Kehadiran tahunan</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <i class="fas fa-check-circle fa-2x mb-2"></i>
                <h4>{{ collect($monthlyStats)->sum('hadir') }}</h4>
                <p class="mb-0">Total Hadir</p>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card bg-warning text-white">
            <div class="card-body text-center">
                <i class="fas fa-hand-paper fa-2x mb-2"></i>
                <h4>{{ collect($monthlyStats)->sum('izin') }}</h4>
                <p class="mb-0">Total Izin</p>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <i class="fas fa-thermometer-half fa-2x mb-2"></i>
                <h4>{{ collect($monthlyStats)->sum('sakit') }}</h4>
                <p class="mb-0">Total Sakit</p>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card bg-danger text-white">
            <div class="card-body text-center">
                <i class="fas fa-times-circle fa-2x mb-2"></i>
                <h4>{{ collect($monthlyStats)->sum('alpha') }}</h4>
                <p class="mb-0">Total Alpha</p>
            </div>
        </div>
    </div>
</div>

<!-- Monthly Trend Analysis -->
<div class="row mb-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-chart-line"></i> Tren Kehadiran Bulanan {{ $year }}</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Bulan</th>
                                <th class="text-center">Total Record</th>
                                <th class="text-center">Hadir</th>
                                <th class="text-center">Izin</th>
                                <th class="text-center">Sakit</th>
                                <th class="text-center">Alpha</th>
                                <th class="text-center">Tingkat Kehadiran</th>
                                <th class="text-center">Tren</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($monthlyStats as $index => $monthData)
                                @php
                                    $monthRate = $monthData['total'] > 0 ? round(($monthData['hadir'] / $monthData['total']) * 100, 1) : 0;
                                    $prevMonthRate = $index > 0 ? ($monthlyStats[$index-1]['total'] > 0 ? round(($monthlyStats[$index-1]['hadir'] / $monthlyStats[$index-1]['total']) * 100, 1) : 0) : 0;
                                    $trend = $monthRate - $prevMonthRate;
                                @endphp
                                <tr>
                                    <td>
                                        <strong>{{ $monthData['month_name'] }}</strong>
                                    </td>
                                    <td class="text-center">{{ $monthData['total'] }}</td>
                                    <td class="text-center">
                                        <span class="text-success fw-bold">{{ $monthData['hadir'] }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="text-warning fw-bold">{{ $monthData['izin'] }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="text-info fw-bold">{{ $monthData['sakit'] }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="text-danger fw-bold">{{ $monthData['alpha'] }}</span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex flex-column align-items-center">
                                            <strong class="text-{{ $monthRate >= 85 ? 'success' : ($monthRate >= 70 ? 'warning' : 'danger') }}">
                                                {{ $monthRate }}%
                                            </strong>
                                            <div class="progress mt-1" style="width: 80px; height: 4px;">
                                                <div class="progress-bar bg-{{ $monthRate >= 85 ? 'success' : ($monthRate >= 70 ? 'warning' : 'danger') }}" 
                                                     style="width: {{ $monthRate }}%"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        @if($index == 0)
                                            <span class="text-muted">-</span>
                                        @elseif($trend > 0)
                                            <span class="text-success">
                                                <i class="fas fa-arrow-up"></i> +{{ number_format($trend, 1) }}%
                                            </span>
                                        @elseif($trend < 0)
                                            <span class="text-danger">
                                                <i class="fas fa-arrow-down"></i> {{ number_format($trend, 1) }}%
                                            </span>
                                        @else
                                            <span class="text-muted">
                                                <i class="fas fa-minus"></i> 0%
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Trend Insights -->
                <div class="mt-3">
                    @php
                        $bestMonth = collect($monthlyStats)->sortByDesc(function($month) {
                            return $month['total'] > 0 ? ($month['hadir'] / $month['total']) * 100 : 0;
                        })->first();
                        
                        $worstMonth = collect($monthlyStats)->filter(function($month) {
                            return $month['total'] > 0;
                        })->sortBy(function($month) {
                            return ($month['hadir'] / $month['total']) * 100;
                        })->first();
                    @endphp
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="alert alert-success">
                                <i class="fas fa-trophy"></i>
                                <strong>Bulan Terbaik:</strong> {{ $bestMonth['month_name'] ?? 'N/A' }}
                                ({{ $bestMonth && $bestMonth['total'] > 0 ? round(($bestMonth['hadir'] / $bestMonth['total']) * 100, 1) : 0 }}% kehadiran)
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>Perlu Perhatian:</strong> {{ $worstMonth['month_name'] ?? 'N/A' }}
                                ({{ $worstMonth && $worstMonth['total'] > 0 ? round(($worstMonth['hadir'] / $worstMonth['total']) * 100, 1) : 0 }}% kehadiran)
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <!-- Performance Metrics -->
        <div class="card mb-3">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0"><i class="fas fa-chart-pie"></i> Analisis Performa</h6>
            </div>
            <div class="card-body">
                @php
                    $excellentMonths = collect($monthlyStats)->filter(function($month) {
                        return $month['total'] > 0 && (($month['hadir'] / $month['total']) * 100) >= 90;
                    })->count();
                    
                    $goodMonths = collect($monthlyStats)->filter(function($month) {
                        $rate = $month['total'] > 0 ? (($month['hadir'] / $month['total']) * 100) : 0;
                        return $rate >= 80 && $rate < 90;
                    })->count();
                    
                    $poorMonths = collect($monthlyStats)->filter(function($month) {
                        return $month['total'] > 0 && (($month['hadir'] / $month['total']) * 100) < 70;
                    })->count();
                @endphp
                
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Excellent (≥90%)</span>
                        <span class="badge bg-success">{{ $excellentMonths }} bulan</span>
                    </div>
                    <div class="progress mb-2" style="height: 8px;">
                        <div class="progress-bar bg-success" style="width: {{ ($excellentMonths / 12) * 100 }}%"></div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Good (80-89%)</span>
                        <span class="badge bg-primary">{{ $goodMonths }} bulan</span>
                    </div>
                    <div class="progress mb-2" style="height: 8px;">
                        <div class="progress-bar bg-primary" style="width: {{ ($goodMonths / 12) * 100 }}%"></div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Need Attention (<70%)</span>
                        <span class="badge bg-danger">{{ $poorMonths }} bulan</span>
                    </div>
                    <div class="progress mb-2" style="height: 8px;">
                        <div class="progress-bar bg-danger" style="width: {{ ($poorMonths / 12) * 100 }}%"></div>
                    </div>
                </div>
                
                <div class="mt-3 text-center">
                    <h5 class="text-{{ $yearlyRate >= 85 ? 'success' : ($yearlyRate >= 70 ? 'warning' : 'danger') }}">
                        {{ $yearlyRate }}%
                    </h5>
                    <small class="text-muted">Rata-rata Tahunan</small>
                </div>
            </div>
        </div>
        
        <!-- Year Goals -->
        <div class="card">
            <div class="card-header bg-warning text-white">
                <h6 class="mb-0"><i class="fas fa-target"></i> Target & Pencapaian</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <small>Target Minimal (85%)</small>
                        <small class="text-{{ $yearlyRate >= 85 ? 'success' : 'danger' }}">
                            {{ $yearlyRate >= 85 ? 'Tercapai' : 'Belum Tercapai' }}
                        </small>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-warning" style="width: 85%"></div>
                        <div class="progress-bar bg-{{ $yearlyRate >= 85 ? 'success' : 'danger' }}" 
                             style="width: {{ max(0, $yearlyRate - 85) }}%"></div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <small>Target Ideal (90%)</small>
                        <small class="text-{{ $yearlyRate >= 90 ? 'success' : 'muted' }}">
                            {{ $yearlyRate >= 90 ? 'Tercapai' : 'Belum Tercapai' }}
                        </small>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-info" style="width: 90%"></div>
                        <div class="progress-bar bg-{{ $yearlyRate >= 90 ? 'success' : 'secondary' }}" 
                             style="width: {{ max(0, $yearlyRate - 90) }}%"></div>
                    </div>
                </div>
                
                @if($yearlyRate < 85)
                    <div class="alert alert-warning">
                        <small>
                            <i class="fas fa-exclamation-triangle"></i>
                            Perlu peningkatan {{ 85 - $yearlyRate }}% untuk mencapai target minimal.
                        </small>
                    </div>
                @elseif($yearlyRate >= 90)
                    <div class="alert alert-success">
                        <small>
                            <i class="fas fa-trophy"></i>
                            Excellent! Target ideal tercapai.
                        </small>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Class Performance Ranking (if showing all classes) -->
@if($classId == 'all' && !empty($classRanking))
    <div class="card">
        <div class="card-header">
            <h6 class="mb-0"><i class="fas fa-medal"></i> Ranking Performa Kelas Tahun {{ $year }}</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th width="10%">Rank</th>
                            <th width="25%">Kelas</th>
                            <th width="15%" class="text-center">Total Siswa</th>
                            <th width="15%" class="text-center">Total Record</th>
                            <th width="15%" class="text-center">Hadir</th>
                            <th width="10%" class="text-center">Tingkat Kehadiran</th>
                            <th width="10%" class="text-center">Penghargaan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($classRanking as $index => $classData)
                            <tr>
                                <td class="text-center">
                                    @if($index == 0)
                                        <span class="badge bg-warning fs-6"><i class="fas fa-trophy"></i> 1</span>
                                    @elseif($index == 1)
                                        <span class="badge bg-secondary fs-6"><i class="fas fa-medal"></i> 2</span>
                                    @elseif($index == 2)
                                        <span class="badge bg-dark fs-6"><i class="fas fa-award"></i> 3</span>
                                    @else
                                        <span class="badge bg-light text-dark">{{ $index + 1 }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <strong>{{ $classData['class']->name }}</strong>
                                        @if($index < 3)
                                            <i class="fas fa-star text-warning ms-2"></i>
                                        @endif
                                    </div>
                                    <small class="text-muted">{{ $classData['class']->grade }} - {{ $classData['class']->major }}</small>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary">{{ $classData['total_students'] }}</span>
                                </td>
                                <td class="text-center">{{ $classData['total_attendances'] }}</td>
                                <td class="text-center">
                                    <span class="text-success fw-bold">{{ $classData['hadir_count'] }}</span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex flex-column align-items-center">
                                        <strong class="text-{{ $classData['attendance_rate'] >= 85 ? 'success' : ($classData['attendance_rate'] >= 70 ? 'warning' : 'danger') }}">
                                            {{ $classData['attendance_rate'] }}%
                                        </strong>
                                        <div class="progress mt-1" style="width: 60px; height: 4px;">
                                            <div class="progress-bar bg-{{ $classData['attendance_rate'] >= 85 ? 'success' : ($classData['attendance_rate'] >= 70 ? 'warning' : 'danger') }}" 
                                                 style="width: {{ $classData['attendance_rate'] }}%"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    @if($classData['attendance_rate'] >= 95)
                                        <span class="badge bg-warning"><i class="fas fa-crown"></i> Champion</span>
                                    @elseif($classData['attendance_rate'] >= 90)
                                        <span class="badge bg-success"><i class="fas fa-star"></i> Excellent</span>
                                    @elseif($classData['attendance_rate'] >= 80)
                                        <span class="badge bg-primary"><i class="fas fa-thumbs-up"></i> Good</span>
                                    @elseif($classData['attendance_rate'] >= 70)
                                        <span class="badge bg-warning"><i class="fas fa-exclamation-triangle"></i> Fair</span>
                                    @else
                                        <span class="badge bg-danger"><i class="fas fa-times-circle"></i> Needs Improvement</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Achievement Summary -->
            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="card bg-success text-white">
                        <div class="card-body text-center">
                            <h4>{{ collect($classRanking)->where('attendance_rate', '>=', 90)->count() }}</h4>
                            <p class="mb-0">Kelas Excellent</p>
                            <small>(≥90% kehadiran)</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-warning text-white">
                        <div class="card-body text-center">
                            <h4>{{ collect($classRanking)->whereBetween('attendance_rate', [80, 89.9])->count() }}</h4>
                            <p class="mb-0">Kelas Good</p>
                            <small>(80-89% kehadiran)</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-danger text-white">
                        <div class="card-body text-center">
                            <h4>{{ collect($classRanking)->where('attendance_rate', '<', 70)->count() }}</h4>
                            <p class="mb-0">Perlu Perhatian</p>
                            <small>(<70% kehadiran)</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- Executive Summary & Recommendations -->
<div class="row mt-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0"><i class="fas fa-lightbulb"></i> Ringkasan Eksekutif & Rekomendasi</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-success"><i class="fas fa-check-circle"></i> Pencapaian Positif</h6>
                        <ul class="list-unstyled">
                            @if($yearlyRate >= 85)
                                <li><i class="fas fa-star text-warning"></i> Target kehadiran tahunan tercapai</li>
                            @endif
                            @if($excellentMonths >= 6)
                                <li><i class="fas fa-calendar-check text-success"></i> {{ $excellentMonths }} bulan dengan performa excellent</li>
                            @endif
                            @if(!empty($classRanking) && collect($classRanking)->where('attendance_rate', '>=', 90)->count() > 0)
                                <li><i class="fas fa-trophy text-warning"></i> {{ collect($classRanking)->where('attendance_rate', '>=', 90)->count() }} kelas mencapai excellent</li>
                            @endif
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-warning"><i class="fas fa-exclamation-triangle"></i> Area Perbaikan</h6>
                        <ul class="list-unstyled">
                            @if($yearlyRate < 85)
                                <li><i class="fas fa-arrow-up text-info"></i> Tingkatkan kehadiran sebesar {{ 85 - $yearlyRate }}%</li>
                            @endif
                            @if($poorMonths > 0)
                                <li><i class="fas fa-calendar-times text-danger"></i> {{ $poorMonths }} bulan dengan kehadiran rendah</li>
                            @endif
                            @if(!empty($classRanking) && collect($classRanking)->where('attendance_rate', '<', 70)->count() > 0)
                                <li><i class="fas fa-focus text-danger"></i> {{ collect($classRanking)->where('attendance_rate', '<', 70)->count() }} kelas perlu perhatian khusus</li>
                            @endif
                        </ul>
                    </div>
                </div>
                
                <div class="mt-3">
                    <h6 class="text-primary"><i class="fas fa-tasks"></i> Rencana Tindak Lanjut</h6>
                    <div class="alert alert-info">
                        <strong>Rekomendasi untuk tahun {{ $year + 1 }}:</strong>
                        <ol class="mb-0 mt-2">
                            <li>Implementasi sistem reward untuk kelas dengan kehadiran terbaik</li>
                            <li>Program mentoring untuk kelas dengan tingkat kehadiran rendah</li>
                            <li>Monitoring bulanan yang lebih intensif</li>
                            <li>Kerjasama dengan orang tua siswa yang sering alpha</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0"><i class="fas fa-chart-pie"></i> Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('kepala-sekolah.reports.monthly', ['month' => now()->format('Y-m')]) }}" class="btn btn-success">
                        <i class="fas fa-calendar-alt"></i> Lihat Bulan Ini
                    </a>
                    
                    <a href="{{ route('kepala-sekolah.reports.daily', ['date' => now()->format('Y-m-d')]) }}" class="btn btn-primary">
                        <i class="fas fa-calendar-day"></i> Lihat Hari Ini
                    </a>
                    
                    <button type="button" class="btn btn-warning" onclick="exportReport('excel')">
                        <i class="fas fa-download"></i> Export Laporan
                    </button>
                    
                    <a href="{{ route('kepala-sekolah.classes.index') }}" class="btn btn-info">
                        <i class="fas fa-school"></i> Kelola Kelas
                    </a>
                    
                    <a href="{{ route('kepala-sekolah.students.index') }}" class="btn btn-secondary">
                        <i class="fas fa-users"></i> Data Siswa
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form when inputs change
    document.querySelector('select[name="year"]').addEventListener('change', function() {
        this.form.submit();
    });
    
    document.querySelector('select[name="class_id"]').addEventListener('change', function() {
        this.form.submit();
    });
    
    document.querySelector('select[name="view"]').addEventListener('change', function() {
        this.form.submit();
    });
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
    typeInput.value = 'yearly';
    form.appendChild(typeInput);
    
    const formatInput = document.createElement('input');
    formatInput.type = 'hidden';
    formatInput.name = 'format';
    formatInput.value = format;
    form.appendChild(formatInput);
    
    // Add current filters
    const yearInput = document.createElement('input');
    yearInput.type = 'hidden';
    yearInput.name = 'year';
    yearInput.value = '{{ $year }}';
    form.appendChild(yearInput);
    
    const classInput = document.createElement('input');
    classInput.type = 'hidden';
    classInput.name = 'class_id';
    classInput.value = '{{ $classId }}';
    form.appendChild(classInput);
    
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}

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
</script>

<style>
@media print {
    .btn, .form-control, .form-select { display: none !important; }
    .card { border: 1px solid #000 !important; page-break-inside: avoid; }
    .no-print { display: none !important; }
}
</style>
@endpush
@endsection