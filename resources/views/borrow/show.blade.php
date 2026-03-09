@extends('layouts.app')
@section('title', 'Transaction: ' . $borrow->code)
@section('page-title', __('borrow.title'))

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('borrow.index') }}" class="btn btn-light border shadow-sm btn-sm">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h2 class="h4 mb-1 fw-bold text-dark">{{ __('borrow.transaction_detail') }}</h2>
            <div class="d-flex align-items-center gap-2">
                <code class="small text-muted bg-light px-2 rounded">{{ $borrow->code }}</code>
                <span class="badge {{ str_contains($borrow->status_badge, 'warning') ? 'bg-warning' : (str_contains($borrow->status_badge, 'primary') ? 'bg-primary' : (str_contains($borrow->status_badge, 'success') ? 'bg-success' : 'bg-danger')) }} rounded-pill px-3 py-1 extra-small">
                    {{ strtoupper($borrow->status) }}
                </span>
            </div>
        </div>
    </div>
    <div class="d-flex gap-2">
        @if($borrow->status === 'pending')
            @can('approve borrow')
            <form action="{{ route('borrow.approve', $borrow) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-success btn-sm px-4 fw-bold shadow-sm">{{ __('common.approve') }}</button>
            </form>
            <form action="{{ route('borrow.reject', $borrow) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-danger btn-sm px-4 fw-bold shadow-sm">{{ __('common.reject') }}</button>
            </form>
            @endcan
        @elseif($borrow->status === 'borrowed')
            <a href="{{ route('return.create', $borrow) }}" class="btn btn-primary btn-sm px-4 fw-bold shadow-sm d-flex align-items-center gap-2">
                <i class="bi bi-arrow-return-left"></i> {{ __('return.title') }}
            </a>
@endif
    </div>
</div>

<div class="row g-4">
    {{-- Main Content --}}
    <div class="col-lg-8">
        {{-- Borrowed Items --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="mb-0 fw-bold text-dark text-uppercase small tracking-wider">
                    Borrowed Items ({{ $borrow->items->count() }})
                </h6>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="bg-light">
                        <tr class="extra-small text-muted text-uppercase">
                            <th class="ps-4">Inventory Item</th>
                            <th class="text-center">Qty</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($borrow->items as $item)
                        <tr>
                            <td class="ps-4 py-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-light rounded p-1 border shadow-inner" style="width: 45px; height: 45px; overflow: hidden;">
                                        @if($item->inventory->image)
                                            <img src="{{ asset('storage/' . $item->inventory->image) }}" class="img-fluid w-100 h-100 object-fit-cover">
                                        @else
                                            <div class="w-100 h-100 d-flex align-items-center justify-content-center text-muted opacity-25">
                                                <i class="bi bi-box"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark small mb-0">{{ $item->inventory->name }}</div>
                                        <code class="extra-small text-muted">{{ $item->inventory->code }}</code>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center pb-3">
                                <div class="fw-black text-primary">{{ $item->quantity }}</div>
                                <small class="text-muted extra-small text-uppercase">{{ $item->inventory->unit }}</small>
                            </td>
                            <td class="pb-3 text-nowrap">
                                @if($item->quantity_returned >= $item->quantity)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle extra-small">FULLY RETURNED</span>
                                @elseif($item->quantity_returned > 0)
                                    <div class="d-flex flex-column">
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle extra-small mb-1 w-fit">PARTIAL</span>
                                        <small class="text-muted extra-small">Returned: {{ $item->quantity_returned }}</small>
                                    </div>
                                @else
                                    <span class="badge bg-light text-muted border extra-small">PENDING</span>
                                @endif
                            </td>
                            <td class="text-end pe-4 pb-3">
                                @if($item->quantity_returned < $item->quantity && in_array($borrow->status, ['borrowed', 'approved']))
                                    <a href="{{ route('return.create', ['borrow' => $borrow->id, 'item_id' => $item->id]) }}" class="btn btn-outline-primary btn-sm extra-small fw-bold border-2">
                                        RETURN ITEM
                                    </a>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Project & Notes --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="mb-0 fw-bold text-dark text-uppercase small tracking-wider">{{ __('projects.title') }}</h6>
            </div>
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-4 mb-4">
                    <div class="bg-primary text-white rounded-3 d-flex align-items-center justify-content-center shadow" style="width: 48px; height: 48px;">
                        <i class="bi bi-building fs-4"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-0 leading-none">{{ $borrow->project->name }}</h5>
                        <p class="text-muted small mb-0">{{ $borrow->project->location }}</p>
                    </div>
                    <div class="ms-auto">
                        <a href="{{ route('projects.show', $borrow->project) }}" class="btn btn-link btn-sm text-decoration-none fw-bold p-0">View Project <i class="bi bi-arrow-right"></i></a>
                    </div>
                </div>
                <div class="pt-3 border-top">
                    <small class="text-muted text-uppercase extra-small fw-bold d-block mb-2">{{ __('borrow.transaction_notes') }}</small>
                    <p class="small text-dark italic mb-0">"{{ $borrow->notes ?: __('borrow.no_notes') }}"</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Sidebar --}}
    <div class="col-lg-4">
        {{-- Personnel Info --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <h6 class="mb-0 fw-bold text-dark text-uppercase small tracking-wider">{{ __('borrow.personnel') }}</h6>
            </div>
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($borrow->user?->name ?? $borrow->requester?->name ?? 'U') }}&background=4e73df&color=fff&size=40" class="rounded-circle shadow-sm">
                    <div>
                        <small class="text-muted text-uppercase extra-small fw-bold d-block mb-1">{{ __('borrow.pic') }}</small>
                        <h6 class="fw-bold text-dark mb-0">{{ $borrow->user?->name ?? $borrow->requester?->name ?? '—' }}</h6>
                    </div>
                </div>
                @if($borrow->approver)
                <div class="d-flex align-items-center gap-3 pt-3 border-top">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($borrow->approver->name) }}&background=1cc88a&color=fff&size=40" class="rounded-circle shadow-sm">
                    <div>
                        <small class="text-muted text-uppercase extra-small fw-bold d-block mb-1">{{ __('borrow.approved_by') }}</small>
                        <h6 class="fw-bold text-dark mb-0">{{ $borrow->approver->name }}</h6>
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Timeline Card --}}
        <div class="card border-0 shadow-sm overflow-hidden mb-4">
            <div class="bg-dark p-4">
                <div class="mb-4">
                    <small class="text-muted text-uppercase extra-small fw-bold d-block mb-1">{{ __('borrow.request_date') }}</small>
                    <p class="text-white fw-bold mb-0 small">{{ $borrow->created_at->translatedFormat('d F Y, H:i') }}</p>
                </div>
                <div>
                    <small class="text-muted text-uppercase extra-small fw-bold d-block mb-1">{{ __('borrow.deadline') }}</small>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <h4 class="fw-black mb-0 {{ $borrow->is_overdue ? 'text-danger' : 'text-success' }}">
                            {{ $borrow->expected_return_date->translatedFormat('d M Y') }}
                        </h4>
                        @if($borrow->is_overdue)
                            <span class="badge bg-danger animate-bounce extra-small">OVERDUE</span>
                        @endif
                    </div>
                    <p class="text-muted mb-0 extra-small text-uppercase fw-bold">{{ $borrow->expected_return_date->diffForHumans() }}</p>
                </div>
            </div>
        </div>

        {{-- Success Slips --}}
        @if($borrow->status === 'returned' || $borrow->status === 'completed')
        <div class="card border-success bg-success bg-opacity-10 border-2 dashed-border">
            <div class="card-body p-4 text-center">
                <div class="text-success mb-2"><i class="bi bi-patch-check-fill fs-3"></i></div>
                <h6 class="fw-bold text-dark">Transaction Completed</h6>
                <p class="extra-small text-muted mb-0">Items were returned on {{ $borrow->actual_return_date?->translatedFormat('d M Y') ?? '—' }}.</p>
            </div>
        </div>
        @endif
    </div>
</div>

<style>
    .fw-black { font-weight: 900; }
    .extra-small { font-size: 0.65rem; }
    .w-fit { width: fit-content; }
    .dashed-border { border-style: dashed !important; }
    .bg-success-subtle { background-color: rgba(25, 135, 84, 0.1); }
    .bg-warning-subtle { background-color: rgba(255, 193, 7, 0.1); }
    .animate-bounce { animation: bounce 1s infinite; }
    @keyframes bounce { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-3px); } }
</style>
@endsection
