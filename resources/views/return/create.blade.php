@extends('layouts.app')

@section('title', __('return.create'))

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row justify-content-center">
        <div class="col-xl-11">
            
            {{-- Header with Back Button --}}
            <div class="d-flex align-items-center justify-content-between mb-5 fade-in">
                <div class="d-flex align-items-center gap-4">
                    <a href="{{ route('borrow.show', $borrow) }}" class="btn btn-outline-dark rounded-circle p-2 d-flex align-items-center justify-content-center shadow-sm" style="width: 45px; height: 45px;">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                    <div>
                        <h1 class="h3 fw-black text-dark text-uppercase tracking-tighter mb-0">Catat Pengembalian Barang</h1>
                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-3 py-1 mt-2 extra-small fw-black tracking-widest">
                            TRANSAKSI: {{ $borrow->code }}
                        </span>
                    </div>
                </div>
            </div>

            <form action="{{ route('return.store', ['borrow' => $borrow->id]) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="borrow_item_id" value="{{ $targetItem->id }}">

                <div class="row g-4">
                    {{-- Summary & Status --}}
                    <div class="col-lg-12 mb-4">
                        <div class="card border-0 shadow-sm rounded-4 primary-gradient text-white overflow-hidden">
                            <div class="card-body p-5 position-relative">
                                <i class="bi bi-arrow-down-left-circle position-absolute text-white opacity-10" style="font-size: 10rem; bottom: -20px; right: -20px;"></i>
                                <div class="row align-items-center relative z-1">
                                    <div class="col-md-5">
                                        <p class="extra-small fw-bold text-uppercase tracking-widest text-white text-opacity-75 mb-2">Aset Target</p>
                                        <h2 class="fw-black mb-1">{{ $targetItem->inventory->name }}</h2>
                                        <div class="d-flex align-items-center gap-2 mt-3">
                                            <span class="badge bg-white bg-opacity-20 text-white border border-white border-opacity-25 px-3 py-2 rounded-pill extra-small fw-bold">
                                                {{ $borrow->project?->name ?? 'Operasi Internal' }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-3 text-md-center mt-4 mt-md-0 border-start border-white border-opacity-10">
                                        <p class="extra-small fw-bold text-uppercase tracking-widest text-white text-opacity-75 mb-2">Total Dipinjam</p>
                                        <h1 class="fw-black mb-0">{{ $targetItem->quantity }} <small class="fs-6 fw-normal text-white text-opacity-50 ms-1">{{ strtoupper($targetItem->inventory->unit) }}</small></h1>
                                    </div>
                                    <div class="col-md-4 text-md-end mt-4 mt-md-0 border-start border-white border-opacity-10">
                                        <p class="extra-small fw-bold text-uppercase tracking-widest text-white text-opacity-75 mb-2">Belum Kembali</p>
                                        <h1 class="fw-black mb-0 text-warning">{{ $targetItem->quantity - $targetItem->quantity_returned }} <small class="fs-6 fw-normal text-white text-opacity-50 ms-1">{{ strtoupper($targetItem->inventory->unit) }}</small></h1>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Qty Controls --}}
                    <div class="col-lg-7">
                        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                            <div class="card-header bg-transparent border-0 p-4 pb-0">
                                <h5 class="fw-black text-dark text-uppercase tracking-wider small mb-0">Rincian Kondisi Barang</h5>
                            </div>
                            <div class="card-body p-4">
                                @php $maxToReturn = $targetItem->quantity - $targetItem->quantity_returned; @endphp
                                
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <div class="bg-success bg-opacity-5 border border-success border-opacity-10 rounded-4 p-4">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <label class="font-label fw-black text-success text-uppercase small tracking-widest">
                                                    <i class="bi bi-shield-check me-2"></i>Kondisi Baik
                                                </label>
                                            </div>
                                            <input type="number" name="quantity_good" id="qty-good" min="0" max="{{ $maxToReturn }}" value="{{ old('quantity_good', $maxToReturn) }}" class="form-control form-control-lg border-0 bg-transparent fw-black text-success fs-2 shadow-none p-0" required oninput="updateTotal()">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="bg-info bg-opacity-5 border border-info border-opacity-10 rounded-4 p-4">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <label class="font-label fw-black text-info text-uppercase small tracking-widest">
                                                    <i class="bi bi-exclamation-square me-2"></i>Rusak Ringan (Poor)
                                                </label>
                                            </div>
                                            <input type="number" name="quantity_poor" id="qty-poor" min="0" max="{{ $maxToReturn }}" value="{{ old('quantity_poor', 0) }}" class="form-control form-control-lg border-0 bg-transparent fw-black text-info fs-2 shadow-none p-0" required oninput="updateTotal()">
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="bg-warning bg-opacity-5 border border-warning border-opacity-20 rounded-4 p-4">
                                            <div class="mb-3">
                                                <label class="font-label fw-black text-warning text-uppercase small tracking-widest">
                                                    <i class="bi bi-exclamation-triangle me-2"></i>Rusak Berat
                                                </label>
                                            </div>
                                            <input type="number" name="quantity_damaged" id="qty-damaged" min="0" max="{{ $maxToReturn }}" value="{{ old('quantity_damaged', 0) }}" class="form-control border-0 bg-transparent fw-black text-warning fs-3 shadow-none p-0" oninput="updateTotal()">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="bg-danger bg-opacity-5 border border-danger border-opacity-20 rounded-4 p-4">
                                            <div class="mb-3">
                                                <label class="font-label fw-black text-danger text-uppercase small tracking-widest">
                                                    <i class="bi bi-x-circle me-2"></i>Hilang
                                                </label>
                                            </div>
                                            <input type="number" name="quantity_lost" id="qty-lost" min="0" max="{{ $maxToReturn }}" value="{{ old('quantity_lost', 0) }}" class="form-control border-0 bg-transparent fw-black text-danger fs-3 shadow-none p-0" oninput="updateTotal()">
                                        </div>
                                    </div>
                                </div>

                                <div id="total-summary-card" class="mt-5 p-4 rounded-4 bg-dark text-white shadow-lg transition-all">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <p class="extra-small fw-bold text-uppercase tracking-widest text-white text-opacity-50 mb-1">Total Sesi Ini</p>
                                            <h2 id="total-display" class="fw-black mb-0">{{ $maxToReturn }} <small class="fs-6 fw-normal text-white text-opacity-50">/ {{ $maxToReturn }}</small></h2>
                                        </div>
                                        <div id="total-status-icon" class="rounded-circle bg-success text-white shadow-sm d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                            <i class="bi bi-check-lg fs-3"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Photo & Documentation --}}
                    <div class="col-lg-5">
                        <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
                            <div class="card-header bg-transparent border-0 p-4 pb-0">
                                <h5 class="fw-black text-dark text-uppercase tracking-wider small mb-0">Documentation</h5>
                            </div>
                            <div class="card-body p-4">
                                <div class="mb-4">
                                    <label class="form-label extra-small fw-black text-muted text-uppercase tracking-wider">Catatan Kondisi & Kerusakan</label>
                                    <textarea name="condition_notes" rows="3" class="form-control bg-light border-0 rounded-3 p-3 small" placeholder="Jelaskan detail kerusakan atau kehilangan jika ada...">{{ old('condition_notes') }}</textarea>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label extra-small fw-black text-muted text-uppercase tracking-wider">Keterangan Tambahan</label>
                                    <textarea name="notes" rows="2" class="form-control bg-light border-0 rounded-3 p-3 small" placeholder="Observasi lainnya...">{{ old('notes') }}</textarea>
                                </div>
                                <div>
                                    <label class="form-label extra-small fw-black text-muted text-uppercase tracking-wider d-block mb-3">Foto Bukti</label>
                                    <div class="d-flex align-items-center gap-4 p-3 bg-light rounded-4 border-2 border-dashed border-gray-300">
                                        <div id="photo-preview" class="rounded-3 bg-white d-flex align-items-center justify-content-center shadow-sm" style="width: 90px; height: 90px; overflow: hidden; border: 2px solid white;">
                                            <i class="bi bi-camera text-muted fs-2"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <p class="extra-small text-muted fw-bold mb-2">Unggah bukti visual untuk barang rusak atau hilang.</p>
                                            <label class="btn btn-dark btn-sm rounded-pill px-4 fw-bold">
                                                PILIH FILE
                                                <input type="file" name="return_photo" class="d-none" accept="image/*" onchange="previewPhoto(this)">
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary btn-lg rounded-4 py-3 fw-black tracking-widest shadow-lg hover-scale">
                                <i class="bi bi-shield-lock-fill me-2"></i>KONFIRMASI PENGEMBALIAN
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .fw-black { font-weight: 900; }
    .extra-small { font-size: 0.65rem; }
    .tracking-tighter { letter-spacing: -0.05em; }
    .primary-gradient { background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); }
    .font-label { font-family: 'Inter', sans-serif; }
    
    .hover-scale { transition: transform 0.2s; }
    .hover-scale:hover { transform: scale(1.02); }

    .fade-in {
        animation: fadeIn 0.8s ease-out forwards;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection

@push('scripts')
<script>
const maxQty = {{ $targetItem->quantity - $targetItem->quantity_returned }};

function updateTotal() {
    const good = parseInt(document.getElementById('qty-good').value) || 0;
    const poor = parseInt(document.getElementById('qty-poor').value) || 0;
    const damaged = parseInt(document.getElementById('qty-damaged').value) || 0;
    const lost = parseInt(document.getElementById('qty-lost').value) || 0;
    const total = good + poor + damaged + lost;

    const display = document.getElementById('total-display');
    const iconContainer = document.getElementById('total-status-icon');
    const summaryCard = document.getElementById('total-summary-card');

    display.innerHTML = `${total} <small class="text-white text-opacity-50 fs-6">/ ${maxQty} {{ strtoupper($targetItem->inventory->unit) }}</small>`;

    if (total === 0) {
        iconContainer.innerHTML = '<i class="bi bi-x-lg fs-3"></i>';
        iconContainer.className = 'rounded-circle bg-danger text-white shadow-sm d-flex align-items-center justify-content-center';
        summaryCard.classList.replace('bg-dark', 'bg-danger');
    } else if (total < maxQty) {
        iconContainer.innerHTML = '<i class="bi bi-exclamation-lg fs-3"></i>';
        iconContainer.className = 'rounded-circle bg-warning text-white shadow-sm d-flex align-items-center justify-content-center';
        summaryCard.classList.replace('bg-danger', 'bg-dark');
        summaryCard.classList.replace('bg-success', 'bg-dark');
    } else if (total > maxQty) {
        iconContainer.innerHTML = '<i class="bi bi-shield-slash fs-3"></i>';
        iconContainer.className = 'rounded-circle bg-danger text-white shadow-sm d-flex align-items-center justify-content-center';
        summaryCard.classList.replace('bg-dark', 'bg-danger');
    } else {
        iconContainer.innerHTML = '<i class="bi bi-check-lg fs-3"></i>';
        iconContainer.className = 'rounded-circle bg-success text-white shadow-sm d-flex align-items-center justify-content-center';
        summaryCard.classList.replace('bg-danger', 'bg-dark');
        summaryCard.classList.replace('bg-dark', 'bg-success');
    }
}

function previewPhoto(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById('photo-preview').innerHTML = `<img src="${e.target.result}" class="img-fluid w-100 h-100 object-fit-cover">`;
            document.getElementById('photo-preview').classList.replace('bg-white', 'bg-transparent');
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush
