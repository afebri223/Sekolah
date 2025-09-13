<!-- resources/views/dashboards/admin.blade.php -->
@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h2><i class="fas fa-tachometer-alt"></i> Dashboard Administrator</h2>
        <p class="text-muted">Selamat datang di dashboard administrator sistem sekolah</p>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
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
                <a href="{{ route('admin.users.index') }}?role=guru" class="text-white text-decoration-none">
                    <small><i class="fas fa-eye"></i> Lihat Detail</small>
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5>Kepala Sekolah</h5>
                        <h2>{{ $stats['total_kepala_sekolah'] }}</h2>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-user-tie fa-2x"></i>
                    </div>
                </div>
                <a href="{{ route('admin.users.index') }}?role=kepala_sekolah" class="text-white text-decoration-none">
                    <small><i class="fas fa-eye"></i> Lihat Detail</small>
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-warning text-white">
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
                <a href="{{ route('admin.classes.index') }}" class="text-white text-decoration-none">
                    <small><i class="fas fa-eye"></i> Lihat Detail</small>
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-info text-white">
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
                <a href="{{ route('admin.students.index') }}" class="text-white text-decoration-none">
                    <small><i class="fas fa-eye"></i> Lihat Detail</small>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Management Menu -->
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-users-cog"></i> Manajemen Data</h5>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <a href="{{ route('admin.users.index') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-users text-primary"></i>
                            <strong>Kelola Pengguna</strong>
                            <small class="d-block text-muted">Admin, Kepala Sekolah, Guru</small>
                        </div>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                    
                    <a href="{{ route('admin.classes.index') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-door-open text-success"></i>
                            <strong>Kelola Kelas</strong>
                            <small class="d-block text-muted">Tambah, Edit, Hapus Kelas</small>
                        </div>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                    
                    <a href="{{ route('admin.students.index') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-user-graduate text-warning"></i>
                            <strong>Kelola Siswa</strong>
                            <small class="d-block text-muted">Data Siswa & Import Bulk</small>
                        </div>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-chart-line"></i> Laporan & Monitoring</h5>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <a href="{{ route('admin.reports.attendance') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-calendar-check text-info"></i>
                            <strong>Laporan Absensi</strong>
                            <small class="d-block text-muted">Detail & Filter Absensi</small>
                        </div>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                    
                    <a href="{{ route('admin.reports.attendance.summary') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-chart-bar text-success"></i>
                            <strong>Ringkasan Bulanan</strong>
                            <small class="d-block text-muted">Rekap Absensi per Kelas</small>
                        </div>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                    
                    <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-cog text-secondary"></i>
                            <strong>Pengaturan Sistem</strong>
                            <small class="d-block text-muted">Konfigurasi Aplikasi</small>
                        </div>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-bolt"></i> Aksi Cepat</h5>
            </div>
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                        <i class="fas fa-user-plus"></i> Tambah User
                    </a>
                    <a href="{{ route('admin.classes.create') }}" class="btn btn-success">
                        <i class="fas fa-plus"></i> Tambah Kelas
                    </a>
                    <a href="{{ route('admin.students.create') }}" class="btn btn-warning">
                        <i class="fas fa-user-graduate"></i> Tambah Siswa
                    </a>
                    <a href="{{ route('admin.reports.attendance') }}" class="btn btn-info">
                        <i class="fas fa-chart-line"></i> Lihat Laporan
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection