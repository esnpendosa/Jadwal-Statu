@extends('layouts.app')
@section('title', __('general.categories'))
@section('page-title', __('general.categories'))

@section('content')
<div class="row mb-5 align-items-center fade-in">
    <div class="col-md-6">
        <h1 class="h2 fw-black text-dark text-uppercase tracking-tighter mb-1">
            Manajemen Kategori
            <span class="d-block h6 fw-bold text-muted text-uppercase tracking-[0.2em] mt-2">Klasifikasi Aset Inventori</span>
        </h1>
    </div>
    <div class="col-md-6 text-md-end mt-3 mt-md-0">
        @can('manage categories')
        <a href="{{ route('categories.create') }}" class="btn btn-primary btn-lg rounded-4 fw-bold shadow-sm px-4">
            <i class="bi bi-plus-circle-fill me-2"></i>TAMBAH KATEGORI
        </a>
        @endcan
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden fade-in" style="animation-delay: 0.1s">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light border-bottom">
                        <tr>
                            <th class="ps-4 py-3 text-uppercase extra-small fw-black text-muted tracking-widest" style="width: 30%;">Nama Kategori</th>
                            <th class="py-3 text-uppercase extra-small fw-black text-muted tracking-widest">Deskripsi</th>
                            <th class="py-3 text-center text-uppercase extra-small fw-black text-muted tracking-widest">Total Aset</th>
                            <th class="text-end pe-4 py-3 text-uppercase extra-small fw-black text-muted tracking-widest">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse($categories as $category)
                        <tr>
                            <td class="ps-4 py-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                                        <i class="bi bi-tag-fill fs-5"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold text-dark mb-0">{{ $category->name }}</h6>
                                        <span class="extra-small text-muted font-monospace">{{ strtoupper($category->slug) }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <p class="small text-muted mb-0 line-clamp-1">{{ $category->description ?: 'Tidak ada deskripsi.' }}</p>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border px-3 py-2 rounded-pill fw-bold">
                                    {{ $category->inventories_count }} <span class="fw-normal text-muted ms-1">Barang</span>
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-2">
                                    @can('manage categories')
                                    <a href="{{ route('categories.edit', $category) }}" class="btn btn-sm btn-light border rounded-circle p-2 shadow-none" title="Ubah" hover-bg-primary>
                                        <i class="bi bi-pencil-fill text-primary"></i>
                                    </a>
                                    <form action="{{ route('categories.destroy', $category) }}" method="POST" class="d-inline" onsubmit="return confirm('Silahkan hapus kategori ini? Pastikan tidak ada barang yang terhubung ke kategori ini.');">
                                        @csrf 
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light border rounded-circle p-2 shadow-none" title="Hapus" hover-bg-danger>
                                            <i class="bi bi-trash3-fill text-danger"></i>
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="py-5 text-center">
                                <i class="bi bi-folder2-open display-4 text-muted opacity-25 d-block mb-3"></i>
                                <span class="text-muted small fw-bold text-uppercase tracking-widest">Belum ada kategori dalam sistem.</span>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($categories->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                {{ $categories->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<style>
    .fw-black { font-weight: 900; }
    .extra-small { font-size: 0.65rem; }
    .tracking-tighter { letter-spacing: -0.05em; }
    .line-clamp-1 { display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden; }

    .fade-in {
        animation: fadeIn 0.8s ease-out forwards;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    [hover-bg-primary]:hover {
        background-color: var(--bs-primary) !important;
        border-color: var(--bs-primary) !important;
    }
    [hover-bg-primary]:hover i {
        color: white !important;
    }

    [hover-bg-danger]:hover {
        background-color: var(--bs-danger) !important;
        border-color: var(--bs-danger) !important;
    }
    [hover-bg-danger]:hover i {
        color: white !important;
    }
</style>
@endsection
