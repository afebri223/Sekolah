@extends('layouts.app')

@section('title', 'Ringkasan Absensi Bulanan')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2><i class="fas fa-chart-bar"></i> Ringkasan Absensi Bulanan</h2>
                <p class="text-muted">Rekap absensi per kelas - {{ date('F Y', mktime(0, 0, 0, $month, 1, $year)) }}</p>
            </div>
            <div>
                <a href="{{ route('admin.reports.attendance') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali ke Laporan
                </a>
                <button onclick="window.print()" class="btn btn-success">
                    <i class="fas fa-print"></i> Print
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Month/Year Selector -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Bulan</label>
                <select name="month" class="form-select">
                    @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                        {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                    </option>
                    @endfor
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Tahun</label>
                <select name="year" class="form-select">
                    @for($y = date('Y'); $y >= date('Y') - 3; $y--)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Tampilkan
                </button>
            </div>
        </form>
    </div>
</div>

@foreach($summary as $classData)
<div class="card mb-4">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-door-open"></i> Kelas {{ $classData['class']->name }}
                <span class="badge bg-secondary ms-2">{{ $classData['total_students'] }} Siswa</span>
            </h5>
            <div>
                @if($classData['class']->waliKelas)
                <small class="text-muted">Wali Kelas: {{ $classData['class']->waliKelas->name }}</small>
                @endif
            </div>
        </div>
    </div>
    <div class="card-body">
        @if(count($classData['attendance_summary']) > 0)
        <div class="table-responsive">
            <table class="table table-striped table-sm">
                <thead>
                    <tr>
                        <th style="width: 5%">No</th>
                        <th style="width: 15%">NIS</th>
                        <th style="width: 25%">Nama Siswa</th>
                        <th style="width: 10%" class="text-center">Hadir</th>
                        <th style="width: 10%" class="text-center">Izin</th>
                        <th style="width: 10%" class="text-center">Sakit</th>
                        <th style="width: 10%" class="text-center">Alpha</th>
                        <th style="width: 10%" class="text-center">Total</th>
                        <th style="width: 15%" class="text-center">% Kehadiran</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalHadir = 0;
                        $totalIzin = 0;
                        $totalSakit = 0;
                        $totalAlpha = 0;
                        $totalDays = 0;
                    @endphp
                    
                    @foreach($classData['attendance_summary'] as $studentData)
                    @php
                        $totalHadir += $studentData['hadir'];
                        $totalIzin += $studentData['izin'];
                        $totalSakit += $studentData['sakit'];
                        $totalAlpha += $studentData['alpha'];
                        $totalDays += $studentData['total_days'];
                    @endphp
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $studentData['student']->nis }}</td>
                        <td>{{ $studentData['student']->name }}</td>
                        <td class="text-center">
                            <span class="badge bg-success">{{ $studentData['hadir'] }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-warning">{{ $studentData['izin'] }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-info">{{ $studentData['sakit'] }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-danger">{{ $studentData['alpha'] }}</span>
                        </td>
                        <td class="text-center">
                            <strong>{{ $studentData['total_days'] }}</strong>
                        </td>
                        <td class="text-center">
                            @if($studentData['total_days'] > 0)
                                @php $rate = $studentData['attendance_rate']; @endphp
                                <span class="badge bg-{{ $rate >= 80 ? 'success' : ($rate >= 60 ? 'warning' : 'danger') }}">
                                    {{ $rate }}%
                                </span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                    
                    <!-- Summary Row -->
                    <tr class="table-dark">
                        <td colspan="3"><strong>TOTAL</strong></td>
                        <td class="text-center"><strong>{{ $totalHadir }}</strong></td>
                        <td class="text-center"><strong>{{ $totalIzin }}</strong></td>
                        <td class="text-center"><strong>{{ $totalSakit }}</strong></td>
                        <td class="text-center"><strong>{{ $totalAlpha }}</strong></td>
                        <td class="text-center"><strong>{{ $totalDays }}</strong></td>
                        <td class="text-center">
                            @if($totalDays > 0)
                                @php $avgRate = round(($totalHadir / $totalDays) * 100, 1); @endphp
                                <strong>{{ $avgRate }}%</strong>
                            @else
                                <strong>-</strong>
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Class Statistics -->
        <div class="row mt-3">
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body text-center p-3">
                        <h4>{{ $totalHadir }}</h4>
                        <small>Total Hadir</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body text-center p-3">
                        <h4>{{ $totalIzin }}</h4>
                        <small>Total Izin</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body text-center p-3">
                        <h4>{{ $totalSakit }}</h4>
                        <small>Total Sakit</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white">
                    <div class="card-body text-center p-3">
                        <h4>{{ $totalAlpha }}</h4>
                        <small>Total Alpha</small>
                    </div>
                </div>
            </div>
        </div>
        @else
        <div class="text-center py-4">
            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
            <h6>Tidak ada data absensi untuk bulan ini</h6>
            <p class="text-muted">Siswa di kelas ini belum memiliki record absensi</p>
        </div>
        @endif
    </div>
</div>
@endforeach

@if(count($summary) == 0)
<div class="card">
    <div class="card-body text-center py-5">
        <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
        <h4>Tidak ada data</h4>
        <p class="text-muted">Belum ada kelas atau data absensi untuk periode yang dipilih</p>
        <a href="{{ route('admin.classes.index') }}" class="btn btn-primary">Kelola Kelas</a>
    </div>
</div>
@endif
@endsection

@section('styles')
<style>
@media print {
    .btn, .card-header .d-flex > div:last-child {
        display: none !important;
    }
    
    .card {
        border: 1px solid #000 !important;
        page-break-inside: avoid;
    }
    
    .table {
        font-size: 12px;
    }
    
    .badge {
        color: #000 !important;
        background-color: #fff !important;
        border: 1px solid #000 !important;
    }
}
</style>
@endsection