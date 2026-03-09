@extends('layouts.app')
@section('title', __('general.categories'))
@section('page-title', __('general.categories'))

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="d-flex align-items-center gap-3 mb-4">
            <a href="{{ route('categories.index') }}" class="btn btn-light btn-sm rounded-circle p-2 shadow-sm">
                <i class="bi bi-arrow-left fs-5"></i>
            </a>
            <h1 class="h3 fw-black text-dark text-uppercase tracking-tighter mb-0">Ubah Kategori</h1>
        </div>

        <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="card-body p-4 p-md-5">
                <div class="mb-4">
                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-1 extra-small fw-black tracking-widest uppercase">ID: {{ $category->id }}</span>
                </div>
                
                <form action="{{ route('categories.update', $category) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-4">
                        <label class="form-label extra-small fw-black text-muted text-uppercase tracking-wider">Nama Kategori</label>
                        <input type="text" name="name" value="{{ old('name', $category->name) }}" 
                            class="form-control form-control-lg bg-light border-0 rounded-3 p-3 small" 
                            placeholder="Contoh: Alat Berat, Suku Cadang, Elektronik" required>
                        @error('name') <div class="text-danger extra-small mt-1 fw-bold">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-5">
                        <label class="form-label extra-small fw-black text-muted text-uppercase tracking-wider">Deskripsi (Opsional)</label>
                        <textarea name="description" rows="4" 
                            class="form-control bg-light border-0 rounded-3 p-3 small" 
                            placeholder="Jelaskan jenis aset yang masuk dalam kategori ini...">{{ old('description', $category->description) }}</textarea>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg rounded-4 py-3 fw-black tracking-widest shadow-lg hover-scale">
                            <i class="bi bi-check-circle-fill me-2"></i>PERBARUI KATEGORI
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .fw-black { font-weight: 900; }
    .extra-small { font-size: 0.65rem; }
    .tracking-tighter { letter-spacing: -0.05em; }
    .hover-scale { transition: transform 0.2s ease; }
    .hover-scale:hover { transform: scale(1.02); }
</style>
@endsection
