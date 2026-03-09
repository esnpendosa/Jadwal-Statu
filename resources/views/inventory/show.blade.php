@extends('layouts.app')
@section('title', $inventory->name)
@section('page-title', __('inventory.title'))

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('inventory.index') }}" class="btn btn-light border shadow-sm btn-sm">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h2 class="h4 mb-1 fw-bold text-dark">{{ $inventory->name }}</h2>
            <div class="d-flex align-items-center gap-2">
                <code class="small text-muted bg-light px-2 rounded">{{ $inventory->code }}</code>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle extra-small">{{ $inventory->category->name }}</span>
                <span class="badge {{ str_contains($inventory->condition_badge, 'success') ? 'bg-success-subtle text-success' : (str_contains($inventory->condition_badge, 'warning') ? 'bg-warning-subtle text-warning' : 'bg-danger-subtle text-danger') }} border extra-small">
                    {{ strtoupper(__('inventory.conditions.' . $inventory->condition)) }}
                </span>
            </div>
        </div>
    </div>
    <div class="d-flex gap-2">
        @can('edit inventory')
        <a href="{{ route('inventory.edit', $inventory) }}" class="btn btn-light border btn-sm px-3">
            <i class="bi bi-pencil me-1"></i> {{ __('common.edit') }}
        </a>
        @endcan
        <button type="button" class="btn btn-primary btn-sm px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#adjustModal">
            <i class="bi bi-plus-slash-minus me-1"></i> {{ __('inventory.adjust_stock') }}
        </button>
    </div>
</div>

<div class="row g-4">
    {{-- Main Content --}}
    <div class="col-lg-8">
        {{-- Counter Cards --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="row g-0">
                <div class="col-sm-4 border-end">
                    <div class="p-4 text-center">
                        <small class="text-uppercase text-muted fw-bold extra-small tracking-wider d-block mb-2">{{ __('inventory.available_stock') }}</small>
                        <h2 class="fw-black text-primary mb-0">{{ $inventory->stock_available }}</h2>
                        <small class="text-muted">{{ $inventory->unit }}</small>
                    </div>
                </div>
                <div class="col-sm-4 border-end">
                    <div class="p-4 text-center">
                        <small class="text-uppercase text-muted fw-bold extra-small tracking-wider d-block mb-2">{{ __('inventory.on_project') }}</small>
                        <h2 class="fw-black text-warning mb-0">{{ $inventory->stock_borrowed }}</h2>
                        <small class="text-muted">{{ $inventory->unit }}</small>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="p-4 text-center">
                        <small class="text-uppercase text-muted fw-bold extra-small tracking-wider d-block mb-2">{{ __('inventory.total_warehouse') }}</small>
                        <h2 class="fw-black text-dark mb-0">{{ $inventory->stock_total }}</h2>
                        <small class="text-muted">{{ $inventory->unit }}</small>
                    </div>
                </div>
            </div>
            <div class="px-4 pb-4">
                <div class="progress" style="height: 8px;">
                    <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $inventory->stock_total > 0 ? ($inventory->stock_available / $inventory->stock_total * 100) : 0 }}%"></div>
                    <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $inventory->stock_total > 0 ? ($inventory->stock_borrowed / $inventory->stock_total * 100) : 0 }}%"></div>
                </div>
                <div class="d-flex justify-content-between mt-2 small text-muted">
                    <span class="extra-small fw-bold"><i class="bi bi-circle-fill text-primary me-1"></i> {{ __('inventory.available') }}</span>
                    <span class="extra-small fw-bold"><i class="bi bi-circle-fill text-warning me-1"></i> {{ __('inventory.borrowed') }}</span>
                    <span class="extra-small fw-bold text-danger">Alert Threshold: {{ $inventory->stock_minimum }}</span>
                </div>
            </div>
        </div>

        {{-- Specs --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="mb-0 fw-bold text-dark">{{ __('inventory.item_specs') }}</h6>
            </div>
            <div class="card-body p-4">
                <div class="row g-4">
                    <div class="col-sm-6">
                        <div class="d-flex gap-3">
                            <div class="bg-light rounded p-2 text-primary"><i class="bi bi-geo-alt fs-5"></i></div>
                            <div>
                                <small class="text-muted text-uppercase extra-small fw-bold d-block">{{ __('inventory.location') }}</small>
                                <span class="fw-bold text-dark">{{ $inventory->location ?: '—' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-top">
                    <small class="text-muted text-uppercase extra-small fw-bold d-block mb-2">{{ __('inventory.description_notes') }}</small>
                    <p class="text-dark small mb-0">{{ $inventory->description ?: 'No additional notes provided.' }}</p>
                </div>
            </div>
        </div>

        {{-- Borrowing --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="mb-0 fw-bold text-dark">{{ __('inventory.active_borrowing') }}</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr class="extra-small text-uppercase text-muted">
                            <th class="ps-4">Code</th>
                            <th>Project</th>
                            <th>PIC</th>
                            <th class="text-center">Qty</th>
                            <th>Deadline</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activeBorrows as $item)
                        <tr class="small">
                            <td class="ps-4 font-monospace text-primary">{{ $item->borrowTransaction->code }}</td>
                            <td><span class="fw-bold">{{ $item->borrowTransaction->project?->name ?? '—' }}</span></td>
                            <td>{{ $item->borrowTransaction->requester?->name ?? '—' }}</td>
                            <td class="text-center fw-bold">{{ $item->quantity }} {{ $inventory->unit }}</td>
                            <td>
                                <span class="{{ $item->borrowTransaction->is_overdue ? 'text-danger fw-black' : 'text-dark fw-semibold' }}">
                                    {{ $item->borrowTransaction->expected_return_date?->translatedFormat('d M Y') }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-4 text-center text-muted small">No active borrowings for this item.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Sidebar --}}
    <div class="col-lg-4">
        {{-- Item Image --}}
        <div class="card border-0 shadow-sm overflow-hidden mb-4">
            <div class="position-relative bg-light group cursor-pointer" style="aspect-ratio: 1/1;">
                @if($inventory->image)
                <img src="{{ asset('storage/' . $inventory->image) }}" class="w-100 h-100 object-fit-cover shadow-inner">
                @else
                <div class="w-100 h-100 d-flex flex-column align-items-center justify-content-center text-light opacity-50">
                    <i class="bi bi-camera fs-1 mb-2"></i>
                    <span class="extra-small fw-bold text-uppercase">No Image</span>
                </div>
                @endif
                @can('edit inventory')
                <a href="{{ route('inventory.edit', $inventory) }}" class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center bg-dark bg-opacity-50 opacity-0 group-hover-opacity transition-all text-white text-decoration-none">
                    <i class="bi bi-pencil me-2"></i> Replace Image
                </a>
                @endcan
            </div>
        </div>

        {{-- QR Code --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-dark border-0 py-3">
                <h6 class="mb-0 fw-bold text-white small text-uppercase tracking-wider">Asset QR Identity</h6>
            </div>
            <div class="card-body text-center p-4">
                <div id="qr-inv-container" class="d-inline-block bg-white p-2 border rounded-3 mb-3">
                    <div id="qr-inv-canvas"></div>
                </div>
                <p class="small text-muted mb-3">Scan this code to quickly view asset details or process borrowing.</p>
                <button onclick="downloadInventoryQR()" class="btn btn-sm btn-light border w-100 fw-bold text-uppercase" style="font-size: 0.65rem">
                    <i class="bi bi-download me-2"></i> Download QR Image
                </button>
            </div>
        </div>

        {{-- Recent Activity --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-dark small text-uppercase">Recent Activity</h6>
                <a href="{{ route('inventory.history', $inventory) }}" class="extra-small fw-bold text-primary text-decoration-none">VIEW ALL</a>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @forelse($inventory->histories()->latest()->take(5)->get() as $history)
                    <div class="list-group-item border-0 p-3 mb-1">
                        <div class="d-flex gap-3">
                            <div class="flex-shrink-0">
                                <div class="rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 32px; height: 32px; font-size: 0.8rem; background: {{ $history->type === 'in' ? '#e8f5e9' : ($history->type === 'out' ? '#fff3e0' : '#e3f2fd') }}; color: {{ $history->type === 'in' ? '#2e7d32' : ($history->type === 'out' ? '#ef6c00' : '#1565c0') }}">
                                    @if($history->type === 'in') <i class="bi bi-arrow-down"></i> @elseif($history->type === 'out') <i class="bi bi-arrow-up"></i> @else <i class="bi bi-arrow-repeat"></i> @endif
                                </div>
                            </div>
                            <div class="flex-grow-1 min-w-0">
                                <div class="d-flex justify-content-between align-items-start">
                                    <h6 class="mb-0 fw-bold small text-dark">{{ ucfirst(str_replace('_', ' ', $history->reference_type)) }}</h6>
                                    <small class="extra-small text-muted">{{ $history->created_at->diffForHumans() }}</small>
                                </div>
                                <p class="mb-0 extra-small text-muted text-truncate">{{ $history->quantity }} {{ $inventory->unit }} — {{ $history->notes }}</p>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="p-4 text-center text-muted small">No recent logs.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Adjustment Modal --}}
<div class="modal fade" id="adjustModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('inventory.adjust-stock', $inventory) }}" method="POST">
                @csrf
                <div class="modal-header bg-light border-0 py-3">
                    <h5 class="modal-title fw-bold text-dark">{{ __('inventory.manual_adjustment') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">Adjustment Type</label>
                        <select id="adjust-type" class="form-select" onchange="updateAdjustmentSign()" required>
                            <option value="in">{{ __('inventory.stock_in') }} (+)</option>
                            <option value="out">{{ __('inventory.stock_out') }} (-)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">Quantity ({{ $inventory->unit }})</label>
                        <input type="number" id="adjust-qty" min="1" value="1" class="form-control" required>
                        <input type="hidden" name="adjustment" id="adjust-value" value="1">
                    </div>
                    <div class="mb-0">
                        <label class="form-label small fw-bold text-muted text-uppercase">Reason / Notes <span class="text-danger">*</span></label>
                        <textarea name="reason" rows="3" class="form-control" required placeholder="Describe why this adjustment is made..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm">Apply Adjustment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .fw-black { font-weight: 900; }
    .extra-small { font-size: 0.65rem; }
    .group:hover .group-hover-opacity { opacity: 1 !important; }
    .btn-success-subtle { background-color: rgba(25, 135, 84, 0.1); }
    .btn-warning-subtle { background-color: rgba(255, 193, 7, 0.1); }
    .btn-danger-subtle { background-color: rgba(220, 53, 69, 0.1); }
</style>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    try {
        new QRCode(document.getElementById('qr-inv-canvas'), {
            text: '{{ url()->current() }}',
            width: 140,
            height: 140,
            colorDark: '#0f172a',
            colorLight: '#ffffff',
            correctLevel: QRCode.CorrectLevel.H
        });
    } catch(e) {
        document.getElementById('qr-inv-canvas').innerHTML = '<p class="small text-muted">{{ $inventory->code }}</p>';
    }
});

function updateAdjustmentSign() {
    const type = document.getElementById('adjust-type').value;
    const qty = parseInt(document.getElementById('adjust-qty').value) || 1;
    document.getElementById('adjust-value').value = type === 'out' ? -qty : qty;
}

document.getElementById('adjust-qty')?.addEventListener('input', function() {
    updateAdjustmentSign();
});

function downloadInventoryQR() {
    const canvas = document.querySelector('#qr-inv-canvas canvas') || document.querySelector('#qr-inv-canvas img');
    if (!canvas) return;
    const link = document.createElement('a');
    link.download = 'asset-{{ $inventory->code }}.png';
    link.href = canvas.tagName === 'CANVAS' ? canvas.toDataURL() : canvas.src;
    link.click();
}
</script>
@endpush
