@extends('layouts.app')
@section('title', __('borrow.title'))
@section('page-title', __('borrow.title'))

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h2 class="h4 mb-1 fw-bold text-dark text-uppercase tracking-tight">{{ __('borrow.title') }}</h2>
        <p class="text-muted extra-small mb-0 fw-bold uppercase tracking-widest">{{ __('borrow.subtitle') }}</p>
    </div>
    @can('create borrow')
    <a href="{{ route('borrow.create') }}" class="btn btn-primary d-flex align-items-center gap-2 px-3 fw-bold shadow-sm">
        <i class="bi bi-plus-lg"></i>
        <span>CREATE REQUEST</span>
    </a>
    @endcan
</div>

{{-- Filters --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <form method="GET" action="{{ route('borrow.index') }}" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label extra-small fw-bold text-muted text-uppercase">{{ __('common.search') }}</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('borrow.search_placeholder') }}" class="form-control border-start-0 ps-0 shadow-none">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label extra-small fw-bold text-muted text-uppercase">{{ __('projects.title') }}</label>
                <select name="project_id" class="form-select form-select-sm shadow-none">
                    <option value="">{{ __('projects.all') }} Projects</option>
                    @foreach($projects as $p)
                    <option value="{{ $p->id }}" {{ request('project_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label extra-small fw-bold text-muted text-uppercase">{{ __('common.status') }}</label>
                <select name="status" class="form-select form-select-sm shadow-none">
                    <option value="">{{ __('common.all') }} Status</option>
                    @foreach(['pending', 'borrowed', 'rejected', 'completed'] as $st)
                    <option value="{{ $st }}" {{ request('status') === $st ? 'selected' : '' }}>{{ strtoupper(__('borrow.status.' . $st)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-dark flex-grow-1 fw-bold">
                    <i class="bi bi-funnel me-1"></i> APPLY FILTERS
                </button>
                <a href="{{ route('borrow.index') }}" class="btn btn-sm btn-light border flex-grow-1 fw-bold">RESET</a>
            </div>
        </form>
    </div>
</div>

{{-- Borrow List --}}
<div class="card border-0 shadow-sm overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr class="extra-small text-muted text-uppercase fw-bold">
                    <th class="ps-4 py-3">{{ __('borrow.detail') }}</th>
                    <th class="py-3">{{ __('projects.title') }}</th>
                    <th class="py-3">{{ __('borrow.pic') }}</th>
                    <th class="text-center py-3">{{ __('borrow.quantity') }}</th>
                    <th class="py-3">{{ __('borrow.deadline') }}</th>
                    <th class="py-3">Status</th>
                    <th class="text-end pe-4 py-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($borrows as $borrow)
                <tr class="small">
                    <td class="ps-4">
                        <div class="d-flex flex-column">
                            <span class="fw-bold text-dark mb-0">
                                @php $firstItem = $borrow->items->first(); @endphp
                                @if($firstItem)
                                    {{ $firstItem->inventory?->name ?? 'Unknown Asset' }}
                                    @if($borrow->items->count() > 1)
                                        <span class="badge bg-primary text-white extra-small ms-1 rounded-pill" style="font-size: 0.55rem">+ {{ $borrow->items->count() - 1 }} OTHERS</span>
                                    @endif
                                @else
                                    <span class="text-danger italic opacity-50">Empty Request</span>
                                @endif
                            </span>
                            <code class="extra-small text-muted font-monospace">{{ $borrow->code }}</code>
                        </div>
                    </td>
                    <td><span class="fw-semibold text-muted tracking-tight">{{ $borrow->project?->name ?? '—' }}</span></td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($borrow->user?->name ?? $borrow->requester?->name ?? 'U') }}&background=f8f9fc&color=4e73df&size=24&bold=true" class="rounded-circle shadow-sm">
                            <span class="fw-bold text-dark">{{ $borrow->user?->name ?? $borrow->requester?->name ?? '—' }}</span>
                        </div>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-white border text-primary fw-black px-3 py-1 extra-small">{{ $borrow->total_quantity }} UNITS</span>
                    </td>
                    <td>
                        @if($borrow->is_overdue && !in_array($borrow->status, ['completed', 'rejected']))
                        <div class="text-danger fw-black">
                            {{ strtoupper($borrow->expected_return_date->translatedFormat('d M Y')) }}
                            <div class="extra-small text-danger blink-red"><i class="bi bi-exclamation-octagon-fill me-1"></i>OVERDUE</div>
                        </div>
                        @else
                        <span class="fw-bold text-muted">{{ strtoupper($borrow->expected_return_date->translatedFormat('d M Y')) }}</span>
                        @endif
                    </td>
                    <td>
                        @php
                            $stBadge = match($borrow->status) {
                                'pending' => 'bg-warning-subtle text-warning border-warning-subtle',
                                'borrowed' => 'bg-primary-subtle text-primary border-primary-subtle',
                                'rejected' => 'bg-danger-subtle text-danger border-danger-subtle',
                                'completed' => 'bg-success-subtle text-success border-success-subtle',
                                default => 'bg-secondary-subtle text-secondary border-secondary-subtle'
                            };
                        @endphp
                        <span class="badge {{ $stBadge }} border px-3 rounded-pill extra-small fw-black shadow-none">{{ strtoupper($borrow->status) }}</span>
                    </td>
                    <td class="text-end pe-4">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('borrow.print', $borrow) }}" target="_blank" class="btn btn-light btn-sm border-0 shadow-none text-dark" title="Print Note">
                                <i class="bi bi-printer-fill"></i>
                            </a>
                            <a href="{{ route('borrow.show', $borrow) }}" class="btn btn-light btn-sm border-0 shadow-none text-primary" title="Details">
                                <i class="bi bi-eye-fill"></i>
                            </a>
                            
                            @if($borrow->status === 'pending')
                                @can('approve borrow')
                                <form action="{{ route('borrow.approve', $borrow) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm border-0 shadow-sm extra-small fw-black text-uppercase px-2 py-0">GRANT</button>
                                </form>
                                <form action="{{ route('borrow.reject', $borrow) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-danger btn-sm border-0 shadow-sm extra-small fw-black text-uppercase px-2 py-0">DENY</button>
                                </form>
                                @endcan
                            @elseif($borrow->status === 'borrowed')
                                <a href="{{ route('return.create', $borrow) }}" class="btn btn-primary btn-sm border-0 shadow-sm extra-small fw-black text-uppercase px-3 py-0">
                                    RETURN ASSETS
                                </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="py-5 text-center">
                        <i class="bi bi-clock-history display-4 text-muted opacity-25 d-block mb-3"></i>
                        <span class="text-muted small fw-bold uppercase tracking-widest">No transaction records matching your request.</span>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($borrows->hasPages())
    <div class="card-footer bg-white border-0 px-4 py-3">
        <div class="pagination-container">
            <p class="extra-small text-muted mb-0 fw-bold uppercase tracking-widest">
                SHOWING {{ $borrows->firstItem() }} TO {{ $borrows->lastItem() }} OF {{ $borrows->total() }} ENTRIES
            </p>
            <div>
                {{ $borrows->links() }}
            </div>
        </div>
    </div>
    @endif
</div>

<style>
    .extra-small { font-size: 0.65rem; }
    .fw-black { font-weight: 900; }
    .bg-primary-subtle { background-color: rgba(78, 115, 223, 0.1); }
    .bg-success-subtle { background-color: rgba(28, 200, 138, 0.1); }
    .bg-warning-subtle { background-color: rgba(246, 194, 62, 0.1); }
    .bg-danger-subtle { background-color: rgba(231, 74, 59, 0.1); }
    .blink-red { animation: blinker 1.5s linear infinite; }
    @keyframes blinker { 50% { opacity: 0; } }
</style>
@endsection
