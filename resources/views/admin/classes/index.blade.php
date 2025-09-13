@extends('layouts.app')

@section('title', 'Kelola Kelas')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2><i class="fas fa-door-open"></i> Kelola Kelas</h2>
                <p class="text-muted">Manajemen kelas dan wali kelas</p>
            </div>
            <div>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                <a href="{{ route('admin.classes.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Kelas
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="card mb-4">
    <div class="card-header">
        <h6 class="mb-0"><i class="fas fa-filter"></i> Filter Kelas</h6>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-2">
                <label class="form-label">Tingkat</label>
                <select name="grade" class="form-select">
                    <option value="">Semua</option>
                    <option value="10" {{ request('grade') == '10' ? 'selected' : '' }}>Kelas 10</option>
                    <option value="11" {{ request('grade') == '11' ? 'selected' : '' }}>Kelas 11</option>
                    <option value="12" {{ request('grade') == '12' ? 'selected' : '' }}>Kelas 12</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Jurusan</label>
                <select name="major" class="form-select">
                    <option value="">Semua</option>
                    <option value="IPA" {{ request('major') == 'IPA' ? 'selected' : '' }}>IPA</option>
                    <option value="IPS" {{ request('major') == 'IPS' ? 'selected' : '' }}>IPS</option>
                    <option value="BAHASA" {{ request('major') == 'BAHASA' ? 'selected' : '' }}>BAHASA</option>
                    <option value="KEJURUAN" {{ request('major') == 'KEJURUAN' ? 'selected' : '' }}>KEJURUAN</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Wali Kelas</label>
                <select name="has_wali_kelas" class="form-select">
                    <option value="">Semua</option>
                    <option value="1" {{ request('has_wali_kelas') == '1' ? 'selected' : '' }}>Ada</option>
                    <option value="0" {{ request('has_wali_kelas') == '0' ? 'selected' : '' }}>Belum Ada</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Cari Kelas</label>
                <input type="text" name="search" class="form-control" 
                       placeholder="Nama kelas..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <div class="d-grid">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="fas fa-search"></i> Filter
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h6 class="mb-0">Daftar Kelas</h6>
            <div class="d-flex gap-2">
                <div class="btn-group btn-group-sm" role="group">
                    <input type="radio" class="btn-check" name="view-mode" id="table-view" checked>
                    <label class="btn btn-outline-secondary" for="table-view">
                        <i class="fas fa-table"></i>
                    </label>
                    
                    <input type="radio" class="btn-check" name="view-mode" id="card-view">
                    <label class="btn btn-outline-secondary" for="card-view">
                        <i class="fas fa-th-large"></i>
                    </label>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <!-- Table View -->
        <div id="table-view-content">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Nama Kelas</th>
                            <th>Tingkat & Jurusan</th>
                            <th>Wali Kelas</th>
                            <th>Siswa</th>
                            <th>Kapasitas</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($classes as $class)
                        <tr>
                            <td>{{ ($classes->currentPage() - 1) * $classes->perPage() + $loop->iteration }}</td>
                            <td>
                                <strong>{{ $class->name }}</strong>
                            </td>
                            <td>
                                <span class="badge bg-info">Kelas {{ $class->grade }}</span>
                                @if($class->major)
                                    <span class="badge bg-secondary">{{ $class->major }}</span>
                                @endif
                            </td>
                            <td>
                                @if($class->waliKelas)
                                    <div>
                                        <strong>{{ $class->waliKelas->name }}</strong>
                                        <br><small class="text-muted">{{ $class->waliKelas->nip }}</small>
                                    </div>
                                @else
                                    <span class="text-muted">
                                        <i class="fas fa-exclamation-triangle text-warning"></i> Belum ada
                                    </span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-primary">{{ $class->students->count() }}</span>
                            </td>
                            <td>{{ $class->capacity }}</td>
                            <td>
                                @php
                                    $studentCount = $class->students->count();
                                    $capacity = $class->capacity;
                                    $percentage = $capacity > 0 ? ($studentCount / $capacity) * 100 : 0;
                                @endphp
                                @if($percentage >= 100)
                                    <span class="badge bg-danger">Penuh</span>
                                @elseif($percentage >= 80)
                                    <span class="badge bg-warning">Hampir Penuh</span>
                                @else
                                    <span class="badge bg-success">Tersedia</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.classes.show', $class) }}" class="btn btn-outline-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.classes.edit', $class) }}" class="btn btn-outline-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.classes.destroy', $class) }}" 
                                          style="display: inline-block;" 
                                          onsubmit="return confirm('Yakin ingin menghapus kelas ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <div>
                                    <i class="fas fa-door-open fa-3x text-muted mb-3"></i>
                                    <h5>Tidak ada data kelas</h5>
                                    @if(request()->hasAny(['grade', 'major', 'has_wali_kelas', 'search']))
                                        <p class="text-muted">Coba ubah filter atau kriteria pencarian</p>
                                        <a href="{{ route('admin.classes.index') }}" class="btn btn-outline-primary">Reset Filter</a>
                                    @else
                                        <p class="text-muted">Belum ada kelas yang dibuat</p>
                                        <a href="{{ route('admin.classes.create') }}" class="btn btn-primary">Buat Kelas Pertama</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Card View -->
        <div id="card-view-content" style="display: none;">
            <div class="row">
                @forelse($classes as $class)
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">{{ $class->name }}</h6>
                            <span class="badge bg-info">{{ $class->grade }}</span>
                        </div>
                        <div class="card-body">
                            @if($class->major)
                            <p class="mb-2"><strong>Jurusan:</strong> {{ $class->major }}</p>
                            @endif
                            <p class="mb-2">
                                <strong>Wali Kelas:</strong> 
                                {{ $class->waliKelas->name ?? 'Belum ada' }}
                            </p>
                            <p class="mb-2">
                                <strong>Siswa:</strong> 
                                {{ $class->students->count() }} / {{ $class->capacity }}
                            </p>
                            <div class="progress mb-2" style="height: 10px;">
                                @php
                                    $percentage = $class->capacity > 0 ? ($class->students->count() / $class->capacity) * 100 : 0;
                                @endphp
                                <div class="progress-bar bg-{{ $percentage >= 100 ? 'danger' : ($percentage >= 80 ? 'warning' : 'success') }}" 
                                     style="width: {{ min($percentage, 100) }}%"></div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('admin.classes.show', $class) }}" class="btn btn-sm btn-outline-info">Detail</a>
                                <a href="{{ route('admin.classes.edit', $class) }}" class="btn btn-sm btn-outline-warning">Edit</a>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12 text-center py-5">
                    <i class="fas fa-door-open fa-3x text-muted mb-3"></i>
                    <h5>Tidak ada data kelas</h5>
                    <p class="text-muted">Belum ada kelas yang dibuat</p>
                    <a href="{{ route('admin.classes.create') }}" class="btn btn-primary">Buat Kelas Pertama</a>
                </div>
                @endforelse
            </div>
        </div>
        
        @if($classes->hasPages())
        <div class="d-flex justify-content-center mt-3">
            {{ $classes->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tableViewRadio = document.getElementById('table-view');
    const cardViewRadio = document.getElementById('card-view');
    const tableViewContent = document.getElementById('table-view-content');
    const cardViewContent = document.getElementById('card-view-content');
    
    tableViewRadio.addEventListener('change', function() {
        if (this.checked) {
            tableViewContent.style.display = 'block';
            cardViewContent.style.display = 'none';
        }
    });
    
    cardViewRadio.addEventListener('change', function() {
        if (this.checked) {
            tableViewContent.style.display = 'none';
            cardViewContent.style.display = 'block';
        }
    });
});
</script>
@endsection