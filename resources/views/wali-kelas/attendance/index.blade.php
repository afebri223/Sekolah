<!-- resources/views/wali-kelas/attendance/index.blade.php -->
@extends('layouts.app')

@section('title', 'Input Absensi - ' . $class->name)

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2><i class="fas fa-clipboard-check"></i> Input Absensi Kelas {{ $class->name }}</h2>
                <p class="text-muted">{{ $class->grade }} {{ $class->major }} - {{ $students->count() }} siswa aktif</p>
            </div>
            <div>
                <a href="{{ route('wali-kelas.dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                <a href="{{ route('wali-kelas.attendance.history') }}" class="btn btn-info">
                    <i class="fas fa-history"></i> Riwayat Absensi
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Date Selection & Quick Stats -->
<div class="row mb-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form method="GET" class="row g-3 align-items-end">
                    <div class="col-md-6">
                        <label class="form-label">Pilih Tanggal Absensi</label>
                        <input type="date" name="date" class="form-control" value="{{ $selectedDate }}" 
                               max="{{ today()->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-calendar-alt"></i> Pilih Tanggal
                        </button>
                    </div>
                    <div class="col-md-3">
                        @if($isAttendanceComplete)
                            <span class="badge bg-success fs-6">
                                <i class="fas fa-check-circle"></i> Absensi Lengkap
                            </span>
                        @else
                            <span class="badge bg-warning fs-6">
                                <i class="fas fa-exclamation-triangle"></i> Belum Lengkap
                            </span>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <h5><i class="fas fa-chart-pie"></i> Statistik Minggu Ini</h5>
                <div class="row">
                    <div class="col-6">
                        <h4>{{ $weeklyStats['hadir'] }}</h4>
                        <small>Hadir</small>
                    </div>
                    <div class="col-6">
                        <h4>{{ round(($weeklyStats['hadir'] / max($weeklyStats['total_possible'], 1)) * 100, 1) }}%</h4>
                        <small>Kehadiran</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Attendance Form -->
<form method="POST" action="{{ route('wali-kelas.attendance.store') }}" id="attendanceForm">
    @csrf
    <input type="hidden" name="date" value="{{ $selectedDate }}">
    
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-list-check"></i> Daftar Absensi - {{ \Carbon\Carbon::parse($selectedDate)->format('d F Y') }}
            </h5>
            <div class="btn-group">
                <button type="button" class="btn btn-sm btn-success" onclick="markAll('hadir')">
                    <i class="fas fa-check"></i> Semua Hadir
                </button>
                <button type="button" class="btn btn-sm btn-warning" onclick="clearAll()">
                    <i class="fas fa-eraser"></i> Reset
                </button>
            </div>
        </div>
        
        <div class="card-body">
            @if($students->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th width="5%">No</th>
                                <th width="15%">NIS</th>
                                <th width="25%">Nama Siswa</th>
                                <th width="35%">Status Kehadiran</th>
                                <th width="20%">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $index => $student)
                                @php
                                    $existingAttendance = $existingAttendances->get($student->id);
                                    $currentStatus = $existingAttendance->status ?? 'hadir';
                                    $currentNotes = $existingAttendance->notes ?? '';
                                @endphp
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><strong>{{ $student->nis }}</strong></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-2">
                                                <span class="avatar-title bg-primary text-white rounded-circle">
                                                    {{ strtoupper(substr($student->name, 0, 1)) }}
                                                </span>
                                            </div>
                                            <div>
                                                <strong>{{ $student->name }}</strong>
                                                <small class="d-block text-muted">{{ $student->gender_full }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="hidden" name="attendances[{{ $index }}][student_id]" value="{{ $student->id }}">
                                        
                                        <div class="btn-group" role="group">
                                            <input type="radio" class="btn-check" name="attendances[{{ $index }}][status]" 
                                                   id="hadir_{{ $student->id }}" value="hadir" 
                                                   {{ $currentStatus === 'hadir' ? 'checked' : '' }}>
                                            <label class="btn btn-outline-success btn-sm" for="hadir_{{ $student->id }}">
                                                <i class="fas fa-check"></i> Hadir
                                            </label>

                                            <input type="radio" class="btn-check" name="attendances[{{ $index }}][status]" 
                                                   id="izin_{{ $student->id }}" value="izin" 
                                                   {{ $currentStatus === 'izin' ? 'checked' : '' }}>
                                            <label class="btn btn-outline-warning btn-sm" for="izin_{{ $student->id }}">
                                                <i class="fas fa-hand-paper"></i> Izin
                                            </label>

                                            <input type="radio" class="btn-check" name="attendances[{{ $index }}][status]" 
                                                   id="sakit_{{ $student->id }}" value="sakit" 
                                                   {{ $currentStatus === 'sakit' ? 'checked' : '' }}>
                                            <label class="btn btn-outline-info btn-sm" for="sakit_{{ $student->id }}">
                                                <i class="fas fa-thermometer-half"></i> Sakit
                                            </label>

                                            <input type="radio" class="btn-check" name="attendances[{{ $index }}][status]" 
                                                   id="alpha_{{ $student->id }}" value="alpha" 
                                                   {{ $currentStatus === 'alpha' ? 'checked' : '' }}>
                                            <label class="btn btn-outline-danger btn-sm" for="alpha_{{ $student->id }}">
                                                <i class="fas fa-times"></i> Alpha
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="text" name="attendances[{{ $index }}][notes]" 
                                               class="form-control form-control-sm" 
                                               placeholder="Keterangan (opsional)" 
                                               value="{{ $currentNotes }}"
                                               maxlength="500">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="row mt-4">
                    <div class="col-md-8">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Petunjuk:</strong>
                            <ul class="mb-0">
                                <li><strong>Hadir:</strong> Siswa masuk sekolah</li>
                                <li><strong>Izin:</strong> Siswa tidak masuk dengan keterangan</li>
                                <li><strong>Sakit:</strong> Siswa tidak masuk karena sakit</li>
                                <li><strong>Alpha:</strong> Siswa tidak masuk tanpa keterangan</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save"></i> 
                                {{ $isAttendanceComplete ? 'Update Absensi' : 'Simpan Absensi' }}
                            </button>
                            
                            @if($isAttendanceComplete)
                                <small class="text-muted text-center">
                                    Terakhir diperbarui: {{ $existingAttendances->first()->updated_at->format('d M Y H:i') }}
                                </small>
                            @endif
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-user-slash fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Tidak ada siswa aktif di kelas ini</h5>
                    <p class="text-muted">Hubungi admin untuk menambah siswa ke kelas.</p>
                </div>
            @endif
        </div>
    </div>
</form>

<!-- Quick Summary Card -->
@if($students->count() > 0)
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-chart-bar"></i> Ringkasan Absensi Hari Ini</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center" id="attendanceSummary">
                        <div class="col-md-3">
                            <div class="border rounded p-3">
                                <h4 class="text-success mb-1" id="hadirCount">0</h4>
                                <small class="text-muted">Hadir</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded p-3">
                                <h4 class="text-warning mb-1" id="izinCount">0</h4>
                                <small class="text-muted">Izin</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded p-3">
                                <h4 class="text-info mb-1" id="sakitCount">0</h4>
                                <small class="text-muted">Sakit</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded p-3">
                                <h4 class="text-danger mb-1" id="alphaCount">0</h4>
                                <small class="text-muted">Alpha</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Update summary when radio buttons change
    function updateSummary() {
        const counts = {
            hadir: 0,
            izin: 0,
            sakit: 0,
            alpha: 0
        };
        
        document.querySelectorAll('input[type="radio"]:checked').forEach(function(radio) {
            counts[radio.value]++;
        });
        
        document.getElementById('hadirCount').textContent = counts.hadir;
        document.getElementById('izinCount').textContent = counts.izin;
        document.getElementById('sakitCount').textContent = counts.sakit;
        document.getElementById('alphaCount').textContent = counts.alpha;
    }
    
    // Listen for changes
    document.querySelectorAll('input[type="radio"]').forEach(function(radio) {
        radio.addEventListener('change', updateSummary);
    });
    
    // Initial summary
    updateSummary();
    
    // Confirmation before submit
    document.getElementById('attendanceForm').addEventListener('submit', function(e) {
        const checkedInputs = document.querySelectorAll('input[type="radio"]:checked').length;
        const totalStudents = {{ $students->count() }};
        
        if (checkedInputs < totalStudents) {
            if (!confirm('Masih ada siswa yang belum diisi status absensinya. Yakin ingin melanjutkan?')) {
                e.preventDefault();
            }
        }
    });
});

// Helper functions
function markAll(status) {
    document.querySelectorAll(`input[value="${status}"]`).forEach(function(radio) {
        radio.checked = true;
    });
    
    // Trigger update
    document.querySelectorAll('input[type="radio"]')[0].dispatchEvent(new Event('change'));
}

function clearAll() {
    document.querySelectorAll('input[type="radio"]').forEach(function(radio) {
        radio.checked = false;
    });
    
    document.querySelectorAll('input[type="text"]').forEach(function(input) {
        input.value = '';
    });
    
    // Reset to default (hadir)
    markAll('hadir');
}

// Auto-save functionality (optional)
let autoSaveTimeout;
document.querySelectorAll('input').forEach(function(input) {
    input.addEventListener('change', function() {
        clearTimeout(autoSaveTimeout);
        autoSaveTimeout = setTimeout(function() {
            // Could implement auto-save via AJAX here
        }, 2000);
    });
});
</script>
@endpush
@endsection