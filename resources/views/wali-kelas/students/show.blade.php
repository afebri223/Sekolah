<!-- resources/views/wali-kelas/students/show.blade.php -->
@extends('layouts.app')

@section('title', 'Detail Siswa - ' . $student->name)

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2><i class="fas fa-user"></i> Detail Siswa</h2>
                <p class="text-muted">Informasi lengkap dan riwayat kehadiran siswa</p>
            </div>
            <div>
                <a href="{{ route('wali-kelas.students.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Student Information -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0"><i class="fas fa-user"></i> Informasi Siswa</h6>
            </div>
            <div class="card-body text-center">
                <div class="avatar-xl mx-auto mb-3">
                    <span class="avatar-title bg-primary text-white rounded-circle fs-1">
                        {{ strtoupper(substr($student->name, 0, 1)) }}
                    </span>
                </div>
                <h5 class="card-title">{{ $student->name }}</h5>
                <p class="text-muted mb-3">{{ $student->nis }}</p>
                
                <div class="row text-center mb-3">
                    <div class="col-6">
                        <div class="border rounded p-2">
                            <i class="fas fa-{{ $student->gender == 'L' ? 'male text-primary' : 'female text-danger' }} fa-lg"></i>
                            <br><small class="text-muted">{{ $student->gender_full }}</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border rounded p-2">
                            <i class="fas fa-birthday-cake text-warning fa-lg"></i>
                            <br><small class="text-muted">{{ $student->birth_date->age }} tahun</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Personal Details -->
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-info-circle"></i> Data Pribadi</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless table-sm">
                    <tr>
                        <td width="40%"><i class="fas fa-map-marker-alt text-muted"></i> Tempat Lahir</td>
                        <td><strong>{{ $student->birth_place }}</strong></td>
                    </tr>
                    <tr>
                        <td><i class="fas fa-calendar text-muted"></i> Tanggal Lahir</td>
                        <td><strong>{{ $student->birth_date->format('d F Y') }}</strong></td>
                    </tr>
                    <tr>
                        <td><i class="fas fa-home text-muted"></i> Alamat</td>
                        <td><strong>{{ Str::limit($student->address, 50) }}</strong></td>
                    </tr>
                    @if($student->phone)
                    <tr>
                        <td><i class="fas fa-phone text-muted"></i> No. HP</td>
                        <td><strong>{{ $student->phone }}</strong></td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>
        
        <!-- Parent Information -->
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-users"></i> Data Orang Tua</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless table-sm">
                    <tr>
                        <td width="40%"><i class="fas fa-user text-muted"></i> Nama</td>
                        <td><strong>{{ $student->parent_name }}</strong></td>
                    </tr>
                    <tr>
                        <td><i class="fas fa-phone text-muted"></i> No. HP</td>
                        <td><strong>{{ $student->parent_phone }}</strong></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Attendance Statistics and History -->
    <div class="col-md-8">
        <!-- Monthly Statistics -->
        <div class="card mb-3">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-chart-line"></i> Statistik Kehadiran (6 Bulan Terakhir)</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($monthlyStats as $stat)
                        <div class="col-md-2 mb-3">
                            <div class="text-center">
                                <div class="progress mx-auto mb-2" style="width: 60px; height: 60px;">
                                    <div class="progress-bar progress-bar-striped bg-{{ $stat['rate'] >= 80 ? 'success' : ($stat['rate'] >= 60 ? 'warning' : 'danger') }}" 
                                         style="width: {{ $stat['rate'] }}%"></div>
                                </div>
                                <small class="text-muted">{{ $stat['month'] }}</small>
                                <br><strong class="text-{{ $stat['rate'] >= 80 ? 'success' : ($stat['rate'] >= 60 ? 'warning' : 'danger') }}">{{ $stat['rate'] }}%</strong>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        
        <!-- Recent Attendance -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="fas fa-history"></i> Riwayat Absensi Terbaru</h6>
                <button type="button" class="btn btn-sm btn-primary" onclick="quickAttendance()">
                    <i class="fas fa-plus"></i> Tambah Absensi
                </button>
            </div>
            <div class="card-body">
                @if($student->attendances->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Status</th>
                                    <th>Keterangan</th>
                                    <th>Dicatat Oleh</th>
                                    <th>Waktu</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($student->attendances->take(20) as $attendance)
                                    <tr>
                                        <td>
                                            <strong>{{ $attendance->date->format('d M Y') }}</strong>
                                            <br><small class="text-muted">{{ $attendance->date->format('l') }}</small>
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
                                                <span class="text-muted">{{ Str::limit($attendance->notes, 30) }}</span>
                                            @else
                                                <span class="text-muted fst-italic">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <small>{{ $attendance->recordedBy->name }}</small>
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ $attendance->created_at->format('H:i') }}</small>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    @if($student->attendances->count() > 20)
                        <div class="text-center mt-3">
                            <a href="{{ route('wali-kelas.attendance.history', ['student_id' => $student->id]) }}" class="btn btn-outline-primary">
                                <i class="fas fa-history"></i> Lihat Semua Riwayat
                            </a>
                        </div>
                    @endif
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                        <h6 class="text-muted">Belum ada riwayat absensi</h6>
                        <p class="text-muted">Siswa belum memiliki riwayat absensi.</p>
                        <button type="button" class="btn btn-primary" onclick="quickAttendance()">
                            <i class="fas fa-plus"></i> Tambah Absensi Pertama
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Quick Attendance Modal -->
<div class="modal fade" id="quickAttendanceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-clipboard-check"></i> Tambah Absensi - {{ $student->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="quickAttendanceForm">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="student_id" value="{{ $student->id }}">
                    
                    <div class="mb-3">
                        <label class="form-label">Tanggal:</label>
                        <input type="date" name="date" class="form-control" value="{{ today()->format('Y-m-d') }}" max="{{ today()->format('Y-m-d') }}">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Status Kehadiran:</label>
                        <div class="row">
                            <div class="col-6 mb-2">
                                <input type="radio" class="btn-check" name="status" id="modal_hadir" value="hadir" checked>
                                <label class="btn btn-outline-success w-100" for="modal_hadir">
                                    <i class="fas fa-check"></i><br>Hadir
                                </label>
                            </div>
                            <div class="col-6 mb-2">
                                <input type="radio" class="btn-check" name="status" id="modal_izin" value="izin">
                                <label class="btn btn-outline-warning w-100" for="modal_izin">
                                    <i class="fas fa-hand-paper"></i><br>Izin
                                </label>
                            </div>
                            <div class="col-6">
                                <input type="radio" class="btn-check" name="status" id="modal_sakit" value="sakit">
                                <label class="btn btn-outline-info w-100" for="modal_sakit">
                                    <i class="fas fa-thermometer-half"></i><br>Sakit
                                </label>
                            </div>
                            <div class="col-6">
                                <input type="radio" class="btn-check" name="status" id="modal_alpha" value="alpha">
                                <label class="btn btn-outline-danger w-100" for="modal_alpha">
                                    <i class="fas fa-times"></i><br>Alpha
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Keterangan (opsional):</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Tambahkan keterangan jika diperlukan..."></textarea>
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
function quickAttendance() {
    // Reset form
    document.getElementById('quickAttendanceForm').reset();
    document.getElementById('modal_hadir').checked = true;
    
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
            
            // Show success message and reload page
            const alert = document.createElement('div');
            alert.className = 'alert alert-success alert-dismissible fade show';
            alert.innerHTML = `
                <i class="fas fa-check-circle"></i> ${data.message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.querySelector('.container-fluid').insertBefore(alert, document.querySelector('.row'));
            
            // Reload page after 2 seconds to show updated data
            setTimeout(() => {
                window.location.reload();
            }, 2000);
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