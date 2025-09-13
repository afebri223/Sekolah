<!-- resources/views/attendance/index.blade.php -->
@extends('layouts.app')

@section('title', 'Input Absensi')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2><i class="fas fa-calendar-check"></i> Input Absensi Kelas {{ $class->name }}</h2>
                <p class="text-muted">Tanggal: {{ date('d F Y') }}</p>
            </div>
            <div>
                <a href="{{ route('wali-kelas.dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>
</div>

@if($todayAttendances->count() > 0)
<div class="alert alert-warning">
    <i class="fas fa-exclamation-triangle"></i> 
    Absensi untuk hari ini sudah pernah diinput. Anda dapat mengubah status absensi siswa di bawah ini.
</div>
@endif

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('attendance.store') }}">
            @csrf
            <input type="hidden" name="date" value="{{ date('Y-m-d') }}">
            
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th style="width: 5%">No</th>
                            <th style="width: 15%">NIS</th>
                            <th style="width: 30%">Nama Siswa</th>
                            <th style="width: 8%">L/P</th>
                            <th style="width: 20%">Status Kehadiran</th>
                            <th style="width: 22%">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $student)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $student->nis }}</td>
                            <td>{{ $student->name }}</td>
                            <td>{{ $student->gender }}</td>
                            <td>
                                @php
                                    $currentStatus = $todayAttendances->get($student->id)->status ?? 'hadir';
                                @endphp
                                <select name="attendances[{{ $student->id }}]" class="form-select form-select-sm" required>
                                    <option value="hadir" {{ $currentStatus == 'hadir' ? 'selected' : '' }}>Hadir</option>
                                    <option value="izin" {{ $currentStatus == 'izin' ? 'selected' : '' }}>Izin</option>
                                    <option value="sakit" {{ $currentStatus == 'sakit' ? 'selected' : '' }}>Sakit</option>
                                    <option value="alpha" {{ $currentStatus == 'alpha' ? 'selected' : '' }}>Alpha</option>
                                </select>
                            </td>
                            <td>
                                @php
                                    $currentNotes = $todayAttendances->get($student->id)->notes ?? '';
                                @endphp
                                <input type="text" name="notes[{{ $student->id }}]" 
                                       class="form-control form-control-sm" 
                                       placeholder="Keterangan..." 
                                       value="{{ $currentNotes }}">
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">Tidak ada data siswa</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($students->count() > 0)
            <div class="text-end mt-3">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-save"></i> Simpan Absensi
                </button>
            </div>
            @endif
        </form>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header">
        <h5><i class="fas fa-info-circle"></i> Keterangan Status Kehadiran</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3">
                <span class="badge bg-success">Hadir</span> - Siswa hadir di kelas
            </div>
            <div class="col-md-3">
                <span class="badge bg-warning">Izin</span> - Siswa izin dengan surat/pemberitahuan
            </div>
            <div class="col-md-3">
                <span class="badge bg-info">Sakit</span> - Siswa sakit dengan surat dokter
            </div>
            <div class="col-md-3">
                <span class="badge bg-danger">Alpha</span> - Siswa tidak hadir tanpa keterangan
            </div>
        </div>
    </div>
</div>
@endsection