@extends('layouts.app')

@section('title', 'Edit Kelas')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-edit"></i> Edit Kelas: {{ $class->name }}</h5>
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

                <form method="POST" action="{{ route('admin.classes.update', $class) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Nama Kelas <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="{{ old('name', $class->name) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="grade" class="form-label">Tingkat <span class="text-danger">*</span></label>
                            <select class="form-select" id="grade" name="grade" required>
                                <option value="">Pilih Tingkat</option>
                                <option value="10" {{ old('grade', $class->grade) == '10' ? 'selected' : '' }}>Kelas 10</option>
                                <option value="11" {{ old('grade', $class->grade) == '11' ? 'selected' : '' }}>Kelas 11</option>
                                <option value="12" {{ old('grade', $class->grade) == '12' ? 'selected' : '' }}>Kelas 12</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="major" class="form-label">Jurusan</label>
                            <select class="form-select" id="major" name="major">
                                <option value="">Pilih Jurusan (Opsional)</option>
                                <option value="IPA" {{ old('major', $class->major) == 'IPA' ? 'selected' : '' }}>IPA</option>
                                <option value="IPS" {{ old('major', $class->major) == 'IPS' ? 'selected' : '' }}>IPS</option>
                                <option value="BAHASA" {{ old('major', $class->major) == 'BAHASA' ? 'selected' : '' }}>BAHASA</option>
                                <option value="AGAMA" {{ old('major', $class->major) == 'AGAMA' ? 'selected' : '' }}>AGAMA</option>
                                <option value="KEJURUAN" {{ old('major', $class->major) == 'KEJURUAN' ? 'selected' : '' }}>KEJURUAN</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="capacity" class="form-label">Kapasitas <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="capacity" name="capacity" 
                                   value="{{ old('capacity', $class->capacity) }}" min="1" max="50" required>
                            <small class="text-muted">Saat ini: {{ $class->students->count() }} siswa</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="wali_kelas_id" class="form-label">Wali Kelas</label>
                        <select class="form-select" id="wali_kelas_id" name="wali_kelas_id">
                            <option value="">Pilih Wali Kelas (Opsional)</option>
                            @foreach($waliKelasOptions as $guru)
                            <option value="{{ $guru->id }}" {{ old('wali_kelas_id', $class->wali_kelas_id) == $guru->id ? 'selected' : '' }}>
                                {{ $guru->name }} - {{ $guru->nip }}
                            </option>
                            @endforeach
                        </select>
                        @if($class->waliKelas)
                        <small class="text-muted">Wali kelas saat ini: {{ $class->waliKelas->name }}</small>
                        @endif
                    </div>

                    @if($class->students->count() > 0)
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> 
                        Kelas ini memiliki {{ $class->students->count() }} siswa aktif. 
                        Pastikan kapasitas tidak kurang dari jumlah siswa yang ada.
                    </div>
                    @endif
                    
                    <div class="text-end">
                        <a href="{{ route('admin.classes.show', $class) }}" class="btn btn-info">
                            <i class="fas fa-eye"></i> Lihat Detail
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection