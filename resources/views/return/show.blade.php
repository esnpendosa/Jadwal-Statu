@extends('layouts.app')

@section('title', 'Return Receipt: ' . $return->code)

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row justify-content-center">
        <div class="col-xl-10">
            
            {{-- Header Actions --}}
            <div class="d-flex align-items-center justify-content-between mb-5 fade-in">
                <div class="d-flex align-items-center gap-4">
                    <a href="{{ route('return.index') }}" class="btn btn-outline-dark rounded-circle p-2 d-flex align-items-center justify-content-center shadow-sm" style="width: 45px; height: 45px;">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                    <div>
                        <h1 class="h3 fw-black text-dark text-uppercase tracking-tighter mb-0">Return Shipment Receipt</h1>
                        <div class="d-flex align-items-center gap-2 mt-2">
                            <span class="badge bg-emerald-500 bg-opacity-10 text-emerald-600 border border-emerald-500 border-opacity-25 px-2 py-1 extra-small fw-black tracking-widest font-monospace">
                                {{ $return->code }}
                            </span>
                            @if($return->is_late)
                                <span class="badge bg-danger text-white px-2 py-1 extra-small fw-black tracking-widest animate-pulse">
                                    LATE RETURN
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('return.print', $return) }}" target="_blank" class="btn btn-dark fw-bold rounded-3 px-4 shadow-sm">
                        <i class="bi bi-printer-fill me-2"></i> CETAK NOTA
                    </a>
                    @can('verify return')
                        @if($return->status === 'pending')
                        <form action="{{ route('return.verify', $return) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-primary fw-bold rounded-3 px-4 shadow-sm">
                                <i class="bi bi-patch-check-fill me-2"></i> VERIFY RETURN
                            </button>
                        </form>
                        @endif
                    @endcan
                </div>
            </div>

            <div class="row g-4">
                {{-- Main Receipt Content --}}
                <div class="col-lg-8">
                    {{-- Asset Summary Card --}}
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 h-100">
                        <div class="card-header border-0 bg-light p-4">
                            <h6 class="fw-black text-muted text-uppercase small tracking-widest mb-0">Transaction Summary</h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-4 align-items-start">
                                <div class="col-md-3">
                                    <div class="ratio ratio-1x1 bg-light rounded-4 border overflow-hidden shadow-inner">
                                        @if($return->borrowItem?->inventory?->image)
                                            <img src="{{ asset('storage/' . $return->borrowItem->inventory->image) }}" class="object-fit-cover">
                                        @else
                                            <div class="d-flex align-items-center justify-content-center text-muted">
                                                <i class="bi bi-box-seam display-6 opacity-25"></i>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-9 border-start ps-md-4">
                                    <h3 class="fw-black text-dark text-uppercase tracking-tighter mb-1">{{ $return->borrowItem?->inventory?->name ?? 'UNKNOWN ASSET' }}</h3>
                                    <p class="text-muted small fw-bold text-uppercase tracking-widest mb-4">CATEGORY: {{ $return->borrowItem?->inventory?->category?->name ?? 'OTHERS' }}</p>
                                    
                                    <div class="row g-3">
                                        <div class="col-4">
                                            <div class="p-3 bg-success rounded-4 text-center shadow-sm">
                                                <p class="extra-small fw-black text-white text-opacity-75 text-uppercase tracking-widest mb-1">Functional</p>
                                                <h4 class="fw-black text-white mb-0">{{ $return->quantity_good }}</h4>
                                                <small class="extra-small text-white text-opacity-75 fw-bold">{{ strtoupper($return->borrowItem?->inventory?->unit ?? 'pcs') }}</small>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="p-3 bg-warning rounded-4 text-center shadow-sm">
                                                <p class="extra-small fw-black text-dark text-opacity-75 text-uppercase tracking-widest mb-1">Damaged</p>
                                                <h4 class="fw-black text-dark mb-0">{{ $return->quantity_damaged }}</h4>
                                                <small class="extra-small text-dark text-opacity-75 fw-bold">{{ strtoupper($return->borrowItem?->inventory?->unit ?? 'pcs') }}</small>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="p-3 bg-danger rounded-4 text-center shadow-sm">
                                                <p class="extra-small fw-black text-white text-opacity-75 text-uppercase tracking-widest mb-1">Missing</p>
                                                <h4 class="fw-black text-white mb-0">{{ $return->quantity_lost }}</h4>
                                                <small class="extra-small text-white text-opacity-75 fw-bold">{{ strtoupper($return->borrowItem?->inventory?->unit ?? 'pcs') }}</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-5 border-top pt-4">
                                <div class="row g-4">
                                    <div class="col-md-6 border-end">
                                        <h6 class="extra-small fw-black text-muted text-uppercase tracking-widest mb-3">Condition Notes</h6>
                                        <div class="p-4 bg-light rounded-4">
                                            <p class="small italic text-dark text-opacity-75 mb-0">"{{ $return->condition_notes ?? 'No condition notes provided.' }}"</p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="extra-small fw-black text-muted text-uppercase tracking-widest mb-3">Operational Remarks</h6>
                                        <p class="small text-muted mb-0">{{ $return->notes ?? 'No additional remarks recorded by officer.' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Metadata Sidebar --}}
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-body p-4">
                            <h6 class="fw-black text-muted text-uppercase extra-small tracking-widest mb-4">Shipment Metadata</h6>
                            
                            <div class="d-flex align-items-center gap-3 mb-4 p-3 bg-light rounded-4">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fs-4 font-monospace shadow-sm" style="width: 45px; height: 45px;">
                                    {{ substr($return->returnedBy?->name ?? 'U', 0, 1) }}
                                </div>
                                <div>
                                    <p class="extra-small fw-bold text-primary text-uppercase tracking-widest mb-0">Returned By</p>
                                    <h6 class="fw-black mb-0 text-dark">{{ $return->returnedBy?->name ?? 'Unknown Officer' }}</h6>
                                </div>
                            </div>

                    <div class="d-flex align-items-center gap-3 mb-4 p-3 bg-white border rounded-4">
                                <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center fs-4 font-monospace shadow-sm" style="width: 45px; height: 45px;">
                                    {{ substr($return->receivedBy?->name ?? 'S', 0, 1) }}
                                </div>
                                <div>
                                    <p class="extra-small fw-bold text-success text-uppercase tracking-widest mb-0">Processed By</p>
                                    <h6 class="fw-black mb-0 text-dark">{{ $return->receivedBy?->name ?? 'System Automated' }}</h6>
                                </div>
                            </div>

                            <div class="space-y-3 ps-2">
                                <div class="mb-3">
                                    <p class="extra-small fw-black text-muted text-uppercase tracking-widest mb-1">Processing Lock</p>
                                    <span class="badge bg-{{ $return->status === 'verified' ? 'success' : 'warning' }} text-white rounded-pill px-3 py-1 extra-small fw-bold">
                                        {{ strtoupper($return->status) }}
                                    </span>
                                </div>
                                <div class="mb-3">
                                    <p class="extra-small fw-black text-muted text-uppercase tracking-widest mb-1">Time Log</p>
                                    <p class="small fw-bold text-dark mb-0">{{ $return->created_at->format('M d, Y • H:i:s') }}</p>
                                    <p class="extra-small text-muted">{{ $return->created_at->diffForHumans() }}</p>
                                </div>
                                @if($return->is_late)
                                    <div class="p-3 bg-danger bg-opacity-5 border border-danger border-opacity-10 rounded-4">
                                        <p class="extra-small fw-black text-danger text-uppercase tracking-widest mb-1">⚠️ SLA VIOLATION</p>
                                        <p class="small fw-bold text-danger mb-0">Asset returned {{ $return->days_late }} days past deadline.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- QR Code Card --}}
                    <div class="card border-0 shadow-sm rounded-4 bg-dark text-white overflow-hidden mb-4">
                        <div class="card-body p-4 text-center">
                            <h6 class="fw-black text-white text-opacity-50 text-uppercase extra-small tracking-widest mb-3">Verification QR</h6>
                            <div id="qr-container" class="bg-white p-3 rounded-4 d-inline-block shadow-lg mx-auto mb-3" style="min-width: 160px; min-height: 160px;">
                                {{-- QR code will render here --}}
                            </div>
                            <div class="mt-2 text-center">
                                <code class="extra-small text-white text-opacity-50 d-block mb-3">{{ $return->code }}</code>
                                <button onclick="downloadQR()" class="btn btn-outline-light btn-sm rounded-pill px-4 extra-small fw-black tracking-widest">DOWNLOAD QR</button>
                            </div>
                        </div>
                    </div>

                    {{-- Evidence Photo --}}
                    @if($return->return_photo)
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                        <div class="card-header border-0 bg-transparent p-4 pb-0">
                            <h6 class="fw-black text-muted text-uppercase extra-small tracking-widest">Visual Evidence</h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="ratio ratio-1x1 rounded-4 overflow-hidden border shadow-sm">
                                <img src="{{ asset('storage/' . $return->return_photo) }}" class="object-fit-cover">
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .fw-black { font-weight: 900; }
    .extra-small { font-size: 0.65rem; }
    .tracking-tighter { letter-spacing: -0.05em; }
    .font-monospace { font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace; }
    
    /* Custom Color Utilities */
    .bg-emerald-500 { background-color: #10b981 !important; }
    .text-emerald-600 { color: #059669 !important; }
    .border-emerald-500 { border-color: #10b981 !important; }
    
    .fade-in {
        animation: fadeIn 0.8s ease-out forwards;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @media print {
        .btn, .card-header button, .container-fluid > div:first-child { display: none !important; }
        .card { border: 1px solid #eee !important; box-shadow: none !important; }
        body { background: white !important; }
    }
</style>
@endsection

@push('scripts')
{{-- QR Code library from CDN --}}
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const returnUrl = '{{ url()->current() }}';
    const returnCode = '{{ $return->code }}';

    try {
        new QRCode(document.getElementById('qr-container'), {
            text: returnUrl,
            width: 140,
            height: 140,
            colorDark: '#0f172a',
            colorLight: '#ffffff',
            correctLevel: QRCode.CorrectLevel.H
        });
    } catch(e) {
        console.error("QR Error:", e);
        document.getElementById('qr-container').innerHTML = `<div class="p-3 text-dark fw-bold font-monospace">${returnCode}</div>`;
    }
});

function downloadQR() {
    const canvas = document.querySelector('#qr-container canvas') || document.querySelector('#qr-container img');
    if (!canvas) return;
    const link = document.createElement('a');
    link.download = 'return-{{ $return->code }}.png';
    if (canvas.tagName === 'CANVAS') {
        link.href = canvas.toDataURL();
    } else {
        link.href = canvas.src;
    }
    link.click();
}
</script>
@endpush
