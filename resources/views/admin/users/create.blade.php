<!-- resources/views/admin/users/create.blade.php -->
@extends('layouts.app')

@section('title', 'Tambah User')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-user-plus"></i> Tambah User Baru</h5>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
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

                <form method="POST" action="{{ route('admin.users.store') }}">
                    @csrf
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="{{ old('name') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="{{ old('email') }}" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                            <select class="form-select" id="role" name="role" required>
                                <option value="">Pilih Role</option>
                                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Administrator</option>
                                <option value="kepala_sekolah" {{ old('role') == 'kepala_sekolah' ? 'selected' : '' }}>Kepala Sekolah</option>
                                <option value="guru" {{ old('role') == 'guru' ? 'selected' : '' }}>Guru</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="nip" class="form-label">NIP</label>
                            <input type="text" class="form-control" id="nip" name="nip" 
                                   value="{{ old('nip') }}">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="phone" class="form-label">Nomor Telepon</label>
                            <input type="text" class="form-control" id="phone" name="phone" 
                                   value="{{ old('phone') }}">
                        </div>
                        <div class="col-md-6" id="wali-kelas-section" style="display: none;">
                            <label class="form-label">Status Wali Kelas</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_wali_kelas" name="is_wali_kelas" 
                                       value="1" {{ old('is_wali_kelas') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_wali_kelas">
                                    Jadikan sebagai Wali Kelas
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="col-md-6">
                            <label for="password_confirmation" class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="password_confirmation" 
                                   name="password_confirmation" required>
                        </div>
                    </div>
                    
                    <div class="text-end">
                        <button type="reset" class="btn btn-secondary">
                            <i class="fas fa-undo"></i> Reset
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.getElementById('role').addEventListener('change', function() {
    const waliKelasSection = document.getElementById('wali-kelas-section');
    if (this.value === 'guru') {
        waliKelasSection.style.display = 'block';
    } else {
        waliKelasSection.style.display = 'none';
        document.getElementById('is_wali_kelas').checked = false;
    }
});

// Trigger on page load if old value exists
if (document.getElementById('role').value === 'guru') {
    document.getElementById('wali-kelas-section').style.display = 'block';
}
</script>
@endsection