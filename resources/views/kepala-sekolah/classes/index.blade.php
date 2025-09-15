<!-- resources/views/kepala-sekolah/classes/index.blade.php -->
@extends('layouts.app')

@section('title', 'Data Kelas')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2><i class="fas fa-school"></i> Data Kelas</h2>
                <p class="text-muted">Monitoring dan overview seluruh kelas di sekolah</p>
            </div>
            <div>
                <a href="{{ route('kepala-sekolah.dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Summary Statistics -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <i class="fas fa-door-open fa-2x mb-2"></i>
                <h4>{{ $classes->total() }}</h4>
                <p class="mb-0">Total Kelas</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <i class="fas fa-user-graduate fa-2x mb-2"></i>
                <h4>{{ $classes->sum(function($class) { return $class->students->count(); }) }}</h4>
                <p class="mb-0">Total Siswa</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <i class="fas fa-chalkboard-teacher fa-2x mb-2"></i>
                <h4>{{ $classes->whereNotNull('wali_kelas_id')->count() }}</h4>
                <p class="mb-0">Memiliki Wali Kelas</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body text-center">
                <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                <h4>{{ $classes->whereNull('wali_kelas_id')->count() }}</h4>
                <p class="mb-0">Belum Ada Wali Kelas</p>
            </div>
        </div>
    </div>
</div>

<!-- Filter & Search -->
<div class="card mb-4">
    <div class="card-header">
        <h6 class="mb-0"><i class="fas fa-filter"></i> Filter & Pencarian</h6>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Tingkat</label>
                <select name="grade" class="form-select">
                    <option value="">Semua Tingkat</option>
                    @foreach($classes->unique('grade')->pluck('grade')->sort() as $grade)
                        <option value="{{ $grade }}" {{ request('grade') == $grade ? 'selected' : '' }}>
                            Kelas {{ $grade }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Status Wali Kelas</label>
                <select name="wali_status" class="form-select">
                    <option value="">Semua</option>
                    <option value="ada" {{ request('wali_status') == 'ada' ? 'selected' : '' }}>Sudah Ada</option>
                    <option value="kosong" {{ request('wali_status') == 'kosong' ? 'selected' : '' }}>Belum Ada</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Cari Kelas</label>
                <input type="text" name="search" class="form-control" placeholder="Nama kelas atau wali kelas..." value="{{ request('search') }}">
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

<!-- Classes List -->
<div class="row">
    @forelse($classes as $class)
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100 border-{{ $class->waliKelas ? 'success' : 'warning' }}">
                <div class="card-header bg-{{ $class->waliKelas ? 'success' : 'warning' }} text-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="fas fa-door-open"></i> {{ $class->name }}
                    </h6>
                    <span class="badge bg-light text-dark">{{ $class->grade }}</span>
                </div>
                
                <div class="card-body">
                    <!-- Basic Info -->
                    <div class="mb-3">
                        @if($class->major)
                            <p class="text-muted mb-1">
                                <i class="fas fa-bookmark"></i> {{ $class->major }}
                            </p>
                        @endif
                        <p class="text-muted mb-2">
                            <i class="fas fa-users"></i> {{ $class->students->count() }}/{{ $class->capacity }} siswa
                        </p>
                        
                        <!-- Capacity Progress -->
                        @php
                            $occupancy = $class->capacity > 0 ? ($class->students->count() / $class->capacity) * 100 : 0;
                        @endphp
                        <div class="progress mb-2" style="height: 6px;">
                            <div class="progress-bar bg-{{ $occupancy >= 90 ? 'danger' : ($occupancy >= 75 ? 'warning' : 'success') }}" 
                                 style="width: {{ $occupancy }}%"></div>
                        </div>
                        <small class="text-muted">{{ round($occupancy, 1) }}% kapasitas</small>
                    </div>
                    
                    <!-- Wali Kelas Info -->
                    <div class="mb-3">
                        <h6 class="text-primary">Wali Kelas:</h6>
                        @if($class->waliKelas)
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm me-2">
                                    <span class="avatar-title bg-success text-white rounded-circle">
                                        {{ strtoupper(substr($class->waliKelas->name, 0, 1)) }}
                                    </span>
                                </div>
                                <div>
                                    <strong>{{ $class->waliKelas->name }}</strong>
                                    <br><small class="text-muted">{{ $class->waliKelas->email }}</small>
                                    @if($class->waliKelas->phone)
                                        <br><small class="text-muted">{{ $class->waliKelas->phone }}</small>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="text-center py-2">
                                <i class="fas fa-user-slash fa-2x text-muted mb-2"></i>
                                <p class="text-muted mb-0">Belum ada wali kelas</p>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Quick Stats -->
                    @php
                        $todayAttendance = $class->attendances()->whereDate('date', today())->count();
                        $todayPresent = $class->attendances()->whereDate('date', today())->where('status', 'hadir')->count();
                        $attendanceRate = $todayAttendance > 0 ? round(($todayPresent / $todayAttendance) * 100, 1) : 0;
                    @endphp
                    
                    <div class="border-top pt-3">
                        <div class="row text-center">
                            <div class="col-6">
                                <small class="text-muted">Absensi Hari Ini</small>
                                <br><strong class="text-primary">{{ $todayAttendance }}</strong>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Tingkat Kehadiran</small>
                                <br><strong class="text-{{ $attendanceRate >= 80 ? 'success' : ($attendanceRate >= 60 ? 'warning' : 'danger') }}">
                                    {{ $attendanceRate }}%
                                </strong>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card-footer bg-light">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('kepala-sekolah.classes.show', $class->id) }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-eye"></i> Detail
                        </a>
                        
                        <div class="btn-group">
                            <a href="{{ route('kepala-sekolah.reports.daily', ['class_id' => $class->id, 'date' => today()->format('Y-m-d')]) }}" 
                               class="btn btn-sm btn-outline-info" title="Laporan Harian">
                                <i class="fas fa-calendar-day"></i>
                            </a>
                            <a href="{{ route('kepala-sekolah.reports.monthly', ['class_id' => $class->id, 'month' => now()->format('Y-m')]) }}" 
                               class="btn btn-sm btn-outline-success" title="Laporan Bulanan">
                                <i class="fas fa-calendar-alt"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-school fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Tidak ada kelas ditemukan</h5>
                    <p class="text-muted">Tidak ada kelas yang sesuai dengan filter yang dipilih.</p>
                </div>
            </div>
        </div>
    @endforelse
</div>

<!-- Pagination -->
@if($classes->hasPages())
    <div class="d-flex justify-content-center">
        {{ $classes->links() }}
    </div>
@endif

<!-- Performance Summary -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-chart-bar"></i> Ringkasan Performa Kelas</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Kelas</th>
                                <th class="text-center">Tingkat</th>
                                <th class="text-center">Kapasitas</th>
                                <th class="text-center">Jumlah Siswa</th>
                                <th class="text-center">Wali Kelas</th>
                                <th class="text-center">Absensi Hari Ini</th>
                                <th class="text-center">Tingkat Kehadiran</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($classes as $class)
                                @php
                                    $todayAttendance = $class->attendances()->whereDate('date', today())->count();
                                    $todayPresent = $class->attendances()->whereDate('date', today())->where('status', 'hadir')->count();
                                    $attendanceRate = $todayAttendance > 0 ? round(($todayPresent / $todayAttendance) * 100, 1) : 0;
                                    $occupancy = $class->capacity > 0 ? ($class->students->count() / $class->capacity) * 100 : 0;
                                @endphp
                                <tr>
                                    <td>
                                        <strong>{{ $class->name }}</strong>
                                        @if($class->major)
                                            <br><small class="text-muted">{{ $class->major }}</small>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-primary">{{ $class->grade }}</span>
                                    </td>
                                    <td class="text-center">{{ $class->capacity }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-{{ $occupancy >= 90 ? 'danger' : ($occupancy >= 75 ? 'warning' : 'success') }}">
                                            {{ $class->students->count() }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @if($class->waliKelas)
                                            <span class="badge bg-success">
                                                <i class="fas fa-check"></i> {{ $class->waliKelas->name }}
                                            </span>
                                        @else
                                            <span class="badge bg-warning">
                                                <i class="fas fa-exclamation-triangle"></i> Belum Ada
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $todayAttendance }}</td>
                                    <td class="text-center">
                                        <strong class="text-{{ $attendanceRate >= 80 ? 'success' : ($attendanceRate >= 60 ? 'warning' : 'danger') }}">
                                            {{ $attendanceRate }}%
                                        </strong>
                                    </td>
                                    <td class="text-center">
                                        @if(!$class->waliKelas)
                                            <span class="badge bg-warning">Perlu Wali Kelas</span>
                                        @elseif($occupancy >= 90)
                                            <span class="badge bg-danger">Penuh</span>
                                        @elseif($attendanceRate >= 85)
                                            <span class="badge bg-success">Baik</span>
                                        @elseif($attendanceRate >= 70)
                                            <span class="badge bg-warning">Cukup</span>
                                        @else
                                            <span class="badge bg-danger">Perlu Perhatian</span>
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
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form when select inputs change
    document.querySelectorAll('select[name="grade"], select[name="wali_status"]').forEach(function(select) {
        select.addEventListener('change', function() {
            this.form.submit();
        });
    });
    
    // Search with debounce
    let searchTimeout;
    document.querySelector('input[name="search"]').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            this.form.submit();
        }, 500);
    });
});
</script>
@endpush
@endsection