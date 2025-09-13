<!-- resources/views/dashboards/guru.blade.php -->
@extends('layouts.app')

@section('title', 'Dashboard Guru')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h2><i class="fas fa-chalkboard-teacher"></i> Dashboard Guru</h2>
        <p class="text-muted">Selamat datang, {{ auth()->user()->name }}</p>
    </div>
</div>

@if(auth()->user()->isWaliKelas())
<div class="alert alert-info">
    <h5><i class="fas fa-info-circle"></i> Status Wali Kelas</h5>
    <p class="mb-2">Anda adalah wali kelas <strong>{{ $stats['kelas'] }}</strong></p>
    <a href="{{ route('wali-kelas.dashboard') }}" class="btn btn-primary btn-sm">
        <i class="fas fa-arrow-right"></i> Akses Dashboard Wali Kelas
    </a>
</div>

<div class="row">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5>Total Siswa</h5>
                        <h2>{{ $stats['total_siswa'] }}</h2>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5>Hadir Hari Ini</h5>
                        <h2>{{ $stats['hadir_hari_ini'] }}</h2>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-check fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5>Tidak Hadir</h5>
                        <h2>{{ $stats['tidak_hadir_hari_ini'] }}</h2>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-times fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5>Kelas</h5>
                        <h3>{{ $stats['kelas'] }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-door-open fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@else
<div class="alert alert-secondary">
    <h5><i class="fas fa-info-circle"></i> Status Guru</h5>
    <p class="mb-0">Anda terdaftar sebagai guru. Jika Anda ingin menjadi wali kelas, silakan hubungi administrator.</p>
</div>
@endif

<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-tasks"></i> Menu Guru</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="list-group">
                            <a href="#" class="list-group-item list-group-item-action">
                                <i class="fas fa-calendar"></i> Jadwal Mengajar
                            </a>
                            <a href="#" class="list-group-item list-group-item-action">
                                <i class="fas fa-book"></i> Materi Pelajaran
                            </a>
                            @if(auth()->user()->isWaliKelas())
                            <a href="{{ route('attendance.index') }}" class="list-group-item list-group-item-action">
                                <i class="fas fa-calendar-check"></i> Input Absensi
                            </a>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="list-group">
                            <a href="#" class="list-group-item list-group-item-action">
                                <i class="fas fa-chart-bar"></i> Nilai Siswa
                            </a>
                            @if(auth()->user()->isWaliKelas())
                            <a href="{{ route('attendance.history') }}" class="list-group-item list-group-item-action">
                                <i class="fas fa-history"></i> Riwayat Absensi
                            </a>
                            @endif
                            <a href="#" class="list-group-item list-group-item-action">
                                <i class="fas fa-user"></i> Profile
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection