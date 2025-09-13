<!-- resources/views/auth/register.blade.php -->
@extends('layouts.app')

@section('title', 'Register - Sistem Sekolah')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header bg-success text-white">
                <h4 class="mb-0"><i class="fas fa-user-plus"></i> Register Pengguna Baru</h4>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="{{ old('name') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="{{ old('email') }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="role" class="form-label">Role</label>
                                <select class="form-select" id="role" name="role" required>
                                    <option value="">Pilih Role</option>
                                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Administrator</option>
                                    <option value="kepala_sekolah" {{ old('role') == 'kepala_sekolah' ? 'selected' : '' }}>Kepala Sekolah</option>
                                    <option value="guru" {{ old('role') == 'guru' ? 'selected' : '' }}>Guru</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nip" class="form-label">NIP (Opsional)</label>
                                <input type="text" class="form-control" id="nip" name="nip" 
                                       value="{{ old('nip') }}">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="phone" class="form-label">Nomor Telepon (Opsional)</label>
                        <input type="text" class="form-control" id="phone" name="phone" 
                               value="{{ old('phone') }}">
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                                <input type="password" class="form-control" id="password_confirmation" 
                                       name="password_confirmation" required>
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-success w-100">
                        <i class="fas fa-user-plus"></i> Register
                    </button>
                </form>
                
                <div class="text-center mt-3">
                    <p>Sudah punya akun? <a href="{{ route('login') }}">Login di sini</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection