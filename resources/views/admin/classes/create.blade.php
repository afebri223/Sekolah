<!-- resources/views/admin/classes/create.blade.php -->
@extends('layouts.app')

@section('title', 'Tambah Kelas')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-plus"></i> Tambah Kelas Baru</h5>
                    <a href="{{ route('admin.classes.index') }}" class="btn btn-secondary btn-sm">
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

                <form method="POST" action="{{ route('admin.classes.store') }}">
                    @csrf
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Nama Kelas <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="{{ old('name') }}" placeholder="Contoh: 10A, 11IPA1" required>
                        </div>
                        <div class="col-md-6">
                            <label for="grade" class="form-label">Tingkat <span class="text-danger">*</span></label>
                            <select class="form-select" id="grade" name="grade" required>
                                <option value="">Pilih Tingkat</option>
                                <option value="10" {{ old('grade') == '10' ? 'selected' : '' }}>Kelas 10</option>
                                <option value="11" {{ old('grade') == '11' ? 'selected' : '' }}>Kelas 11</option>
                                <option value="12" {{ old('grade') == '12' ? 'selected' : '' }}>Kelas 12</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="major" class="form-label">Jurusan</label>
                            <select class="form-select" id="major" name="major">
                                <option value="">Pilih Jurusan (Opsional)</option>
                                <option value="IPA" {{ old('major') == 'IPA' ? 'selected' : '' }}>IPA</option>
                                <option value="IPS" {{ old('major') == 'IPS' ? 'selected' : '' }}>IPS</option>
                                <option value="BAHASA" {{ old('major') == 'BAHASA' ? 'selected' : '' }}>BAHASA</option>
                                <option value="AGAMA" {{ old('major') == 'AGAMA' ? 'selected' : '' }}>AGAMA</option>
                                <option value="KEJURUAN" {{ old('major') == 'KEJURUAN' ? 'selected' : '' }}>KEJURUAN</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="capacity" class="form-label">Kapasitas <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="capacity" name="capacity" 
                                   value="{{ old('capacity', 30) }}" min="1" max="50" required>
                            <small class="text-muted">Maksimal 50 siswa per kelas</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="wali_kelas_id" class="form-label">Wali Kelas</label>
                        <select class="form-select" id="wali_kelas_id" name="wali_kelas_id">
                            <option value="">Pilih Wali Kelas (Opsional)</option>
                            @foreach($waliKelasOptions as $guru)
                            <option value="{{ $guru->id }}" {{ old('wali_kelas_id') == $guru->id ? 'selected' : '' }}>
                                {{ $guru->name }} - {{ $guru->nip }}
                            </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Hanya guru dengan status "wali kelas" yang dapat dipilih</small>
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

<!-- Contoh Kelas Card -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h6><i class="fas fa-info-circle"></i> Contoh Penamaan Kelas</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <strong>SMA:</strong>
                        <ul class="list-unstyled small">
                            <li>10A, 10B, 10C (Kelas 10)</li>
                            <li>11IPA1, 11IPA2 (Kelas 11 IPA)</li>
                            <li>11IPS1, 11IPS2 (Kelas 11 IPS)</li>
                            <li>12IPA1, 12IPS1 (Kelas 12)</li>
                        </ul>
                    </div>
                    <div class="col-md-4">
                        <strong>SMK:</strong>
                        <ul class="list-unstyled small">
                            <li>10TKJ1, 10TKJ2</li>
                            <li>11RPL1, 11RPL2</li>
                            <li>12MM1, 12MM2</li>
                        </ul>
                    </div>
                    <div class="col-md-4">
                        <strong>Tips:</strong>
                        <ul class="list-unstyled small">
                            <li>Gunakan nama yang mudah diingat</li>
                            <li>Konsisten dengan format</li>
                            <li>Sertakan tingkat dan jurusan</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection