@extends('layouts.app')
@section('title', __('inventory.title'))
@section('page-title', __('inventory.title'))

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h2 class="h4 mb-1 fw-bold text-dark text-uppercase tracking-tight">{{ __('inventory.title') }}</h2>
        <p class="text-muted extra-small mb-0 fw-bold uppercase tracking-widest">{{ __('inventory.total_items', ['count' => $inventories->total()]) }} REGISTERED ASSETS</p>
    </div>
    <div class="d-flex gap-2">
        @if($lowStockCount > 0)
        <span class="badge bg-danger-subtle text-danger border border-danger-subtle d-flex align-items-center gap-2 px-3 py-2 rounded-pill extra-small fw-black">
            <i class="bi bi-exclamation-triangle-fill"></i>
            {{ $lowStockCount }} {{ strtoupper(__('inventory.low_stock')) }}
        </span>
        @endif
        @can('create inventory')
        <a href="{{ route('inventory.create') }}" class="btn btn-primary d-flex align-items-center gap-2 px-3 fw-bold shadow-sm">
            <i class="bi bi-plus-lg"></i>
            <span>{{ __('inventory.create') }}</span>
        </a>
        @endcan
    </div>
</div>

{{-- Filters --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <form method="GET" action="{{ route('inventory.index') }}" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label extra-small fw-bold text-uppercase text-muted">{{ __('common.search') }}</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('inventory.search_placeholder') }}" class="form-control border-start-0 ps-0 shadow-none">
                </div>
            </div>
            <div class="col-md-2">
                <label class="form-label extra-small fw-bold text-uppercase text-muted">{{ __('inventory.category') }}</label>
                <select name="category" class="form-select form-select-sm shadow-none">
                    <option value="">{{ __('inventory.all_categories') }}</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label extra-small fw-bold text-uppercase text-muted">{{ __('inventory.condition') }}</label>
                <select name="condition" class="form-select form-select-sm shadow-none">
                    <option value="">{{ __('inventory.all_conditions') }}</option>
                    @foreach(['good','fair','poor','damaged','maintenance'] as $k)
                    <option value="{{ $k }}" {{ request('condition') === $k ? 'selected' : '' }}>{{ __('inventory.conditions.' . $k) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label extra-small fw-bold text-uppercase text-muted">Stock Availability</label>
                <select name="stock_status" class="form-select form-select-sm shadow-none">
                    <option value="">{{ __('common.all') }} Status</option>
                    <option value="low" {{ request('stock_status') === 'low' ? 'selected' : '' }}>{{ __('inventory.low_stock') }} ONLY</option>
                    <option value="available" {{ request('stock_status') === 'available' ? 'selected' : '' }}>AVAILABLE STOCK</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-dark flex-grow-1 fw-bold">
                    <i class="bi bi-funnel me-1"></i> APPLY FILTERS
                </button>
                <a href="{{ route('inventory.index') }}" class="btn btn-sm btn-light border flex-grow-1 fw-bold">RESET</a>
            </div>
        </form>
    </div>
</div>

{{-- Inventory Table --}}
<div class="card border-0 shadow-sm overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr class="extra-small text-muted text-uppercase fw-bold">
                    <th class="ps-4 py-3">{{ __('inventory.code') }}</th>
                    <th class="py-3">{{ __('inventory.name') }}</th>
                    <th class="py-3">{{ __('inventory.category') }}</th>
                    <th class="py-3">{{ __('inventory.stock') }}</th>
                    <th class="py-3">{{ __('inventory.condition') }}</th>
                    <th class="text-end pe-4 py-3">{{ __('common.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($inventories as $item)
                <tr>
                    <td class="ps-4">
                        <code class="px-2 py-1 bg-light text-primary rounded extra-small fw-bold border">{{ $item->code }}</code>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-light rounded overflow-hidden shadow-sm" style="width: 40px; height: 40px;">
                                @if($item->image)
                                <img src="{{ asset('storage/' . $item->image) }}" class="w-100 h-100 object-fit-cover">
                                @else
                                <div class="w-100 h-100 d-flex align-items-center justify-content-center text-muted">
                                    <i class="bi bi-box"></i>
                                </div>
                                @endif
                            </div>
                            <div>
                                <a href="{{ route('inventory.show', $item) }}" class="fw-bold text-dark text-decoration-none hover-primary d-block mb-0">{{ $item->name }}</a>
                                @if($item->is_low_stock)
                                <span class="badge bg-danger rounded-pill extra-small fw-black" style="font-size: 0.55rem; padding: 0.2rem 0.5rem">CRITICAL STOCK</span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge bg-white border text-muted extra-small fw-bold px-3 py-1">{{ strtoupper($item->category?->name ?? 'UNCATEGORIZED') }}</span>
                    </td>
                    <td>
                        <div class="d-flex flex-column">
                            <div class="d-flex align-items-center gap-2">
                                <span class="fw-black text-dark">{{ $item->stock_available }}</span>
                                <span class="extra-small text-muted fw-bold">/ {{ $item->stock_total }} {{ strtoupper($item->unit) }}</span>
                            </div>
                            @if($item->stock_borrowed > 0)
                            <div class="progress mt-1" style="height: 4px; width: 60px;">
                                <div class="progress-bar bg-warning" role="progressbar" style="width: {{ ($item->stock_borrowed / $item->stock_total) * 100 }}%"></div>
                            </div>
                            <small class="text-warning extra-small fw-black mt-1">{{ $item->stock_borrowed }} ON PROJECT</small>
                            @endif
                        </div>
                    </td>
                    <td>
                        @php
                            $badgeClass = match($item->condition) {
                                'good' => 'bg-success',
                                'fair' => 'bg-info',
                                'poor' => 'bg-warning text-dark',
                                'damaged' => 'bg-danger',
                                default => 'bg-secondary'
                            };
                        @endphp
                        <span class="badge {{ $badgeClass }} extra-small fw-black px-3 py-1">
                            {{ strtoupper(__('inventory.conditions.' . $item->condition)) }}
                        </span>
                    </td>
                    <td class="text-end pe-4">
                        <div class="dropdown">
                            <button class="btn btn-light btn-sm border shadow-none" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 py-2 mt-2">
                                <li><a class="dropdown-item py-2 extra-small fw-bold text-uppercase" href="{{ route('inventory.show', $item) }}"><i class="bi bi-eye me-2 text-primary"></i> Digital Twin</a></li>
                                @can('edit inventory')
                                <li><a class="dropdown-item py-2 extra-small fw-bold text-uppercase" href="{{ route('inventory.edit', $item) }}"><i class="bi bi-pencil-square me-2 text-info"></i> Update Asset</a></li>
                                @endcan
                                @can('delete inventory')
                                <li><hr class="dropdown-divider opacity-50"></li>
                                <li>
                                    <form action="{{ route('inventory.destroy', $item) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="button" onclick="if(confirm('Are you sure you want to decommission this asset?')) this.form.submit()" class="dropdown-item py-2 extra-small fw-bold text-uppercase text-danger"><i class="bi bi-trash3 me-2"></i> Decommission</button>
                                    </form>
                                </li>
                                @endcan
                            </ul>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="py-5 text-center">
                        <i class="bi bi-box-seam display-1 text-light opacity-25 d-block mb-3"></i>
                        <span class="text-muted small fw-bold uppercase tracking-widest">No assets matching the current filters.</span>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($inventories->hasPages())
    <div class="card-footer bg-white border-0 px-4 py-3">
        <div class="pagination-container">
            <p class="extra-small text-muted mb-0 fw-bold uppercase tracking-widest">
                SHOWING {{ $inventories->firstItem() }} TO {{ $inventories->lastItem() }} OF {{ $inventories->total() }} ENTRIES
            </p>
            <div>
                {{ $inventories->links() }}
            </div>
        </div>
    </div>
    @endif
</div>

<style>
    .extra-small { font-size: 0.65rem; }
    .fw-black { font-weight: 900; }
    .hover-primary:hover { color: #4e73df !important; }
    .dropdown-item:hover { background-color: #f8f9fc; }
</style>
@endsection
