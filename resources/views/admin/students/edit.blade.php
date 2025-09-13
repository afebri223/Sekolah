@extends('layouts.app')

@section('title', 'Edit Siswa')

@section('content')
<div class="row">
    <div class="col-md-10 mx-auto">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-user-edit"></i> Edit Siswa: {{ $student->name }}</h5>
                    <a href="{{ route('admin.students.index') }}" class="btn btn-secondary btn-sm">
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

                <form method="POST" action="{{ route('admin.students.update', $student) }}">
                    @csrf
                    @method('PUT')
                    
                    <!-- Informasi Dasar -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary border-bottom pb-2"><i class="fas fa-user"></i> Informasi Dasar Siswa</h6>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="nis" class="form-label">NIS <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nis" name="nis" 
                                       value="{{ old('nis', $student->nis) }}" required>
                                <small class="text-muted">Nomor Induk Siswa (unik)</small>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="{{ old('name', $student->name) }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="gender" class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                                <select class="form-select" id="gender" name="gender" required>
                                    <option value="">Pilih</option>
                                    <option value="L" {{ old('gender', $student->gender) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="P" {{ old('gender', $student->gender) == 'P' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="birth_date" class="form-label">Tanggal Lahir <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="birth_date" name="birth_date" 
                                       value="{{ old('birth_date', $student->birth_date->format('Y-m-d')) }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="birth_place" class="form-label">Tempat Lahir <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="birth_place" name="birth_place" 
                                       value="{{ old('birth_place', $student->birth_place) }}" required>
                            </div>
                        </div>
                    </div>

                    <!-- Alamat & Kontak -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-success border-bottom pb-2"><i class="fas fa-home"></i> Alamat & Kontak</h6>
                        </div>
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="address" class="form-label">Alamat Lengkap <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="address" name="address" rows="3" required>{{ old('address', $student->address) }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="phone" class="form-label">Nomor HP Siswa</label>
                                <input type="text" class="form-control" id="phone" name="phone" 
                                       value="{{ old('phone', $student->phone) }}">
                                <small class="text-muted">Opsional</small>
                            </div>
                        </div>
                    </div>

                    <!-- Data Orang Tua -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-warning border-bottom pb-2"><i class="fas fa-users"></i> Data Orang Tua/Wali</h6>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="parent_name" class="form-label">Nama Orang Tua/Wali <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="parent_name" name="parent_name" 
                                       value="{{ old('parent_name', $student->parent_name) }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="parent_phone" class="form-label">Nomor HP Orang Tua <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="parent_phone" name="parent_phone" 
                                       value="{{ old('parent_phone', $student->parent_phone) }}" required>
                            </div>
                        </div>
                    </div>

                    <!-- Data Akademik -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-info border-bottom pb-2"><i class="fas fa-graduation-cap"></i> Data Akademik</h6>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="class_id" class="form-label">Kelas <span class="text-danger">*</span></label>
                                <select class="form-select" id="class_id" name="class_id" required>
                                    <option value="">Pilih Kelas</option>
                                    @foreach($classes as $class)
                                    <option value="{{ $class->id }}" {{ old('class_id', $student->class_id) == $class->id ? 'selected' : '' }}>
                                        {{ $class->name }} - {{ $class->grade }} {{ $class->major }}
                                    </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Kelas saat ini: {{ $student->class->name }}</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status" class="form-label">Status Siswa <span class="text-danger">*</span></label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="active" {{ old('status', $student->status) == 'active' ? 'selected' : '' }}>Aktif</option>
                                    <option value="inactive" {{ old('status', $student->status) == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                                    <option value="graduated" {{ old('status', $student->status) == 'graduated' ? 'selected' : '' }}>Lulus</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    @if($student->attendances->count() > 0)
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> 
                        Siswa ini memiliki {{ $student->attendances->count() }} record absensi. 
                        Perubahan data tidak akan mempengaruhi riwayat absensi.
                    </div>
                    @endif
                    
                    <div class="text-end">
                        <a href="{{ route('admin.students.show', $student) }}" class="btn btn-info">
                            <i class="fas fa-eye"></i> Lihat Detail
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Siswa
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection