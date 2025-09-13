<!-- resources/views/dashboards/kepala-sekolah.blade.php -->
@extends('layouts.app')

@section('title', 'Dashboard Kepala Sekolah')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h2><i class="fas fa-school"></i> Dashboard Kepala Sekolah</h2>
        <p class="text-muted">Selamat datang di dashboard kepala sekolah</p>
    </div>
</div>

<div class="row">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5>Total Guru</h5>
                        <h2>{{ $stats['total_guru'] }}</h2>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-chalkboard-teacher fa-2x"></i>
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
                        <h5>Total Kelas</h5>
                        <h2>{{ $stats['total_kelas'] }}</h2>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-door-open fa-2x"></i>
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
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5>Absensi Hari Ini</h5>
                        <h2>{{ $stats['attendance_today'] }}</h2>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-calendar-check fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-chart-line"></i> Menu Laporan</h5>
            </div>
            <div class="card-body">
                <div class="list-group">
                    <a href="#" class="list-group-item list-group-item-action">
                        <i class="fas fa-chart-bar"></i> Laporan Absensi Bulanan
                    </a>
                    <a href="#" class="list-group-item list-group-item-action">
                        <i class="fas fa-file-alt"></i> Laporan Kinerja Guru
                    </a>
                    <a href="#" class="list-group-item list-group-item-action">
                        <i class="fas fa-graduation-cap"></i> Laporan Prestasi Siswa
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-eye"></i> Monitoring</h5>
            </div>
            <div class="card-body">
                <div class="list-group">
                    <a href="#" class="list-group-item list-group-item-action">
                        <i class="fas fa-users"></i> Data Guru & Staff
                    </a>
                    <a href="#" class="list-group-item list-group-item-action">
                        <i class="fas fa-user-graduate"></i> Data Siswa
                    </a>
                    <a href="#" class="list-group-item list-group-item-action">
                        <i class="fas fa-calendar"></i> Jadwal Akademik
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection