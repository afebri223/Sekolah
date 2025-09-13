<!-- resources/views/welcome.blade.php -->
@extends('layouts.app')

@section('title', 'Selamat Datang')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 text-center">
        <div class="jumbotron bg-primary text-white p-5 rounded">
            <h1 class="display-4 mb-4">
                <i class="fas fa-school"></i> Sistem Manajemen Sekolah
            </h1>
            <p class="lead mb-4">
                Platform digital untuk mengelola administrasi sekolah, absensi siswa, 
                dan koordinasi antara guru, kepala sekolah, dan administrator.
            </p>
            
            <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                <a href="{{ route('login') }}" class="btn btn-light btn-lg me-md-2">
                    <i class="fas fa-sign-in-alt"></i> Login
                </a>
                <a href="{{ route('register') }}" class="btn btn-outline-light btn-lg">
                    <i class="fas fa-user-plus"></i> Register
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row mt-5">
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="fas fa-user-tie fa-3x text-primary mb-3"></i>
                <h4>Administrator</h4>
                <p class="text-muted">
                    Kelola seluruh sistem, pengguna, kelas, dan siswa. 
                    Akses penuh untuk konfigurasi sistem.
                </p>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="fas fa-graduation-cap fa-3x text-success mb-3"></i>
                <h4>Kepala Sekolah</h4>
                <p class="text-muted">
                    Monitor kinerja sekolah, lihat laporan absensi, 
                    dan kelola data guru serta siswa.
                </p>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="fas fa-chalkboard-teacher fa-3x text-warning mb-3"></i>
                <h4>Guru / Wali Kelas</h4>
                <p class="text-muted">
                    Input absensi siswa harian, kelola data kelas, 
                    dan monitor kehadiran siswa.
                </p>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-star"></i> Fitur Utama</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-success"></i> Sistem login multi-role</li>
                            <li><i class="fas fa-check text-success"></i> Manajemen pengguna</li>
                            <li><i class="fas fa-check text-success"></i> Absensi siswa digital</li>
                            <li><i class="fas fa-check text-success"></i> Dashboard khusus setiap role</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-success"></i> Riwayat absensi lengkap</li>
                            <li><i class="fas fa-check text-success"></i> Laporan real-time</li>
                            <li><i class="fas fa-check text-success"></i> Interface responsif</li>
                            <li><i class="fas fa-check text-success"></i> Keamanan data terjamin</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection