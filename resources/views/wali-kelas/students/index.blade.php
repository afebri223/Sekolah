<!-- resources/views/wali-kelas/students/index.blade.php -->
@extends('layouts.app')

@section('title', 'Data Siswa - ' . $class->name)

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2><i class="fas fa-users"></i> Data Siswa Kelas {{ $class->name }}</h2>
                <p class="text-muted">{{ $class->grade }} {{ $class->major }} - {{ $students->count() }} siswa aktif</p>
            </div>
            <div>
                <a href="{{ route('wali-kelas.attendance.index') }}" class="btn btn-primary">
                    <i class="fas fa-clipboard-check"></i> Input Absensi
                </a>
                <a href="{{ route('wali-kelas.dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Dashboard
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Class Summary -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <i class="fas fa-users fa-2x mb-2"></i>
                <h4>{{ $students->count() }}</h4>
                <p class="mb-0">Total Siswa</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <i class="fas fa-male fa-2x mb-2"></i>
                <h4>{{ $students->where('gender', 'L')->count() }}</h4>
                <p class="mb-0">Laki-laki</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <i class="fas fa-female fa-2x mb-2"></i>
                <h4>{{ $students->where('gender', 'P')->count() }}</h4>
                <p class="mb-0">Perempuan</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body text-center">
                <i class="fas fa-chart-line fa-2x mb-2"></i>
                <h4>{{ round($students->avg('recent_rate'), 1) }}%</h4>
                <p class="mb-0">Rata-rata Kehadiran</p>
            </div>
        </div>
    </div>
</div>

<!-- Students List -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0"><i class="fas fa-list"></i> Daftar Siswa</h6>
        <div class="input-group" style="width: 300px;">
            <input type="text" class="form-control" placeholder="Cari nama atau NIS..." id="searchInput">
            <button class="btn btn-outline-secondary" type="button">
                <i class="fas fa-search"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        @if($students->count() > 0)
            <div class="row">
                @foreach($students as $student)
                    <div class="col-md-6 col-lg-4 mb-3" data-student="{{ strtolower($student->name . ' ' . $student->nis) }}">
                        <div class="card border">
                            <div class="card-body">
                                <div class="d-flex align-items-start">
                                    <div class="avatar-lg me-3">
                                        <span class="avatar-title bg-primary text-white rounded-circle fs-4">
                                            {{ strtoupper(substr($student->name, 0, 1)) }}
                                        </span>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="card-title mb-1">{{ $student->name }}</h6>
                                        <p class="card-text">
                                            <small class="text-muted">
                                                <i class="fas fa-id-badge"></i> {{ $student->nis }}<br>
                                                <i class="fas fa-{{ $student->gender == 'L' ? 'male' : 'female' }}"></i> {{ $student->gender_full }}<br>
                                                <i class="fas fa-birthday-cake"></i> {{ $student->birth_date->format('d M Y') }}
                                            </small>
                                        </p>
                                        
                                        <!-- Recent Attendance Stats -->
                                        <div class="mt-2">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <small class="text-muted">Kehadiran 7 hari terakhir:</small>
                                                <span class="badge bg-{{ $student->recent_rate >= 80 ? 'success' : ($student->recent_rate >= 60 ? 'warning' : 'danger') }}">
                                                    {{ $student->recent_rate }}%
                                                </span>
                                            </div>
                                            <div class="progress" style="height: 4px;">
                                                <div class="progress-bar bg-{{ $student->recent_rate >= 80 ? 'success' : ($student->recent_rate >= 60 ? 'warning' : 'danger') }}" 
                                                     style="width: {{ $student->recent_rate }}%"></div>
                                            </div>
                                            <small class="text-muted">
                                                {{ $student->recent_hadir }}/{{ $student->recent_total }} hari hadir
                                            </small>
                                        </div>
                                        
                                        <div class="mt-3">
                                            <a href="{{ route('wali-kelas.students.show', $student->id) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i> Detail
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-success" 
                                                    onclick="quickAttendance({{ $student->id }}, '{{ $student->name }}')">
                                                <i class="fas fa-check"></i> Absen Cepat
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-user-slash fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Tidak ada siswa aktif</h5>
                <p class="text-muted">Belum ada siswa yang terdaftar di kelas ini.</p>
            </div>
        @endif
    </div>
</div>

<!-- Quick Attendance Modal -->
<div class="modal fade" id="quickAttendanceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-clipboard-check"></i> Absensi Cepat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="quickAttendanceForm">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="quickStudentId" name="student_id">
                    <input type="hidden" name="date" value="{{ today()->format('Y-m-d') }}">
                    
                    <div class="mb-3">
                        <label class="form-label">Siswa:</label>
                        <p class="fw-bold" id="quickStudentName"></p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Tanggal:</label>
                        <input type="date" name="date" class="form-control" value="{{ today()->format('Y-m-d') }}" max="{{ today()->format('Y-m-d') }}">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Status Kehadiran:</label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="status" id="quick_hadir" value="hadir" checked>
                            <label class="btn btn-outline-success" for="quick_hadir">
                                <i class="fas fa-check"></i> Hadir
                            </label>

                            <input type="radio" class="btn-check" name="status" id="quick_izin" value="izin">
                            <label class="btn btn-outline-warning" for="quick_izin">
                                <i class="fas fa-hand-paper"></i> Izin
                            </label>

                            <input type="radio" class="btn-check" name="status" id="quick_sakit" value="sakit">
                            <label class="btn btn-outline-info" for="quick_sakit">
                                <i class="fas fa-thermometer-half"></i> Sakit
                            </label>

                            <input type="radio" class="btn-check" name="status" id="quick_alpha" value="alpha">
                            <label class="btn btn-outline-danger" for="quick_alpha">
                                <i class="fas fa-times"></i> Alpha
                            </label>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Keterangan (opsional):</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Tambahkan keterangan jika diperlukan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Absensi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Search functionality
document.getElementById('searchInput').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const studentCards = document.querySelectorAll('[data-student]');
    
    studentCards.forEach(card => {
        const studentData = card.getAttribute('data-student');
        if (studentData.includes(searchTerm)) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
});

// Quick attendance functionality
function quickAttendance(studentId, studentName) {
    document.getElementById('quickStudentId').value = studentId;
    document.getElementById('quickStudentName').textContent = studentName;
    
    // Reset form
    document.getElementById('quickAttendanceForm').reset();
    document.getElementById('quick_hadir').checked = true;
    
    new bootstrap.Modal(document.getElementById('quickAttendanceModal')).show();
}

// Handle quick attendance form submission
document.getElementById('quickAttendanceForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('{{ route("wali-kelas.attendance.quick-mark") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('quickAttendanceModal')).hide();
            
            // Show success message
            const alert = document.createElement('div');
            alert.className = 'alert alert-success alert-dismissible fade show';
            alert.innerHTML = `
                <i class="fas fa-check-circle"></i> ${data.message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.querySelector('.container-fluid').insertBefore(alert, document.querySelector('.row'));
            
            // Auto-hide after 3 seconds
            setTimeout(() => {
                alert.remove();
            }, 3000);
        } else {
            alert('Error: ' + (data.error || 'Terjadi kesalahan'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menyimpan absensi');
    });
});
</script>
@endpush
@endsection