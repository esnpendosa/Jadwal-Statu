@extends('layouts.app')
@section('title', 'Inventory Analysis Report')
@section('page-title', 'Reporting Engine')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col-md-6">
        <h2 class="h4 mb-1 fw-black text-dark tracking-tighter uppercase mb-0">
            <i class="bi bi-graph-up-arrow me-2 text-primary"></i>INVENTORY ANALYTICS
        </h2>
        <p class="text-muted small mb-0">Deep insights into stock movements and asset health</p>
    </div>
    <div class="col-md-6 text-md-end mt-3 mt-md-0">
        <div class="btn-group shadow-sm">
            <a href="{{ route('reports.export', ['type' => 'inventory'] + request()->all()) }}" class="btn btn-primary fw-bold">
                <i class="bi bi-file-earmark-pdf me-2"></i>Export PDF
            </a>
        </div>
    </div>
</div>

{{-- Report Filter --}}
<div class="card border-0 shadow-sm mb-4 overflow-hidden">
    <div class="card-body bg-light bg-opacity-50">
        <form method="GET" action="{{ route('reports.inventory') }}" class="row g-3">
            <div class="col-md-3">
                <label class="form-label extra-small fw-black text-muted text-uppercase tracking-widest">Target Category</label>
                <select name="category" class="form-select form-select-sm border-0 shadow-sm">
                    <option value="">All Asset Categories</option>
                    @foreach($categories as $c)
                    <option value="{{ $c->id }}" {{ request('category') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label extra-small fw-black text-muted text-uppercase tracking-widest">Condition Focus</label>
                <select name="condition" class="form-select form-select-sm border-0 shadow-sm">
                    <option value="">All Conditions</option>
                    @foreach(['good', 'broken', 'maintenance', 'poor'] as $st)
                    <option value="{{ $st }}" {{ request('condition') === $st ? 'selected' : '' }}>{{ strtoupper($st) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label extra-small fw-black text-muted text-uppercase tracking-widest">Search Term</label>
                <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm border-0 shadow-sm" placeholder="Asset name or code...">
            </div>
            <div class="col-md-2 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold">APPLY</button>
                <a href="{{ route('reports.inventory') }}" class="btn btn-light btn-sm w-100 border fw-bold">RESET</a>
            </div>
        </form>
    </div>
</div>

{{-- Stats Cards --}}
<div class="row g-3 mb-4">
    <div class="col-xl-2 col-md-4">
        <div class="card border-0 shadow-sm h-100 border-start border-primary border-4">
            <div class="card-body p-3">
                <small class="text-muted extra-small fw-black text-uppercase tracking-widest d-block mb-1">Total Assets</small>
                <h4 class="fw-black text-dark mb-0">{{ $inventories->count() }}</h4>
            </div>
        </div>
    </div>

    <div class="col-xl-2 col-md-4">
        <div class="card border-0 shadow-sm h-100 border-start border-info border-4">
            <div class="card-body p-3">
                <small class="text-muted extra-small fw-black text-uppercase tracking-widest d-block mb-1">In Warehouse</small>
                <h4 class="fw-black text-info mb-0">{{ $inventories->sum('stock_available') }}</h4>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-6">
        <div class="card border-0 shadow-sm h-100 border-start border-warning border-4">
            <div class="card-body p-3">
                <small class="text-muted extra-small fw-black text-uppercase tracking-widest d-block mb-1">On Project</small>
                <h4 class="fw-black text-warning mb-0">{{ $inventories->sum('stock_total') - $inventories->sum('stock_available') }}</h4>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100 border-start border-danger border-4">
            <div class="card-body p-3">
                <small class="text-muted extra-small fw-black text-uppercase tracking-widest d-block mb-1">Damaged/Poor</small>
                <h4 class="fw-black text-danger mb-0">{{ $inventories->whereIn('condition', ['damaged', 'poor', 'broken'])->count() }}</h4>
            </div>
        </div>
    </div>
</div>

{{-- Report Table --}}
<div class="card border-0 shadow-sm overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr class="extra-small text-muted text-uppercase fw-black tracking-widest">
                    <th class="ps-4">Asset Identification</th>
                    <th>Category</th>
                    <th class="text-center">Total Stock</th>
                    <th class="text-center">Available</th>
                    <th class="text-center">In Use</th>
                    <th>Condition</th>
                    <th class="text-end pe-4">Utilization</th>
                </tr>
            </thead>
            <tbody class="border-top-0">
                @forelse($inventories as $item)
                @php
                    $inUse = $item->stock_total - $item->stock_available;
                    $utilRate = $item->stock_total > 0 ? ($inUse / $item->stock_total) * 100 : 0;
                @endphp
                <tr>
                    <td class="ps-4 py-3">
                        <div class="d-flex align-items-center gap-2">
                            <div class="bg-primary bg-opacity-10 text-primary rounded p-2 d-none d-sm-block">
                                <i class="bi bi-box-seam"></i>
                            </div>
                            <div>
                                <div class="small fw-bold text-dark text-uppercase tracking-tight">{{ $item->name }}</div>
                                <code class="extra-small text-muted font-monospace">#{{ $item->code }}</code>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge bg-light text-dark fw-bold border extra-small">{{ $item->category->name }}</span>
                    </td>
                    <td class="text-center fw-bold">{{ $item->stock_total }}</td>
                    <td class="text-center text-success fw-black">{{ $item->stock_available }}</td>
                    <td class="text-center text-primary fw-black">{{ $inUse }}</td>
                    <td>
                        @php
                            $stClass = match($item->condition) {
                                'good' => 'bg-success',
                                'maintenance', 'poor' => 'bg-warning text-dark',
                                default => 'bg-danger'
                            };
                        @endphp
                        <span class="badge {{ $stClass }} px-2 py-1 extra-small uppercase fw-black rounded-pill">
                            {{ $item->condition }}
                        </span>
                    </td>
                    <td class="text-end pe-4">
                        <div class="d-flex flex-column align-items-end gap-1">
                            <span class="extra-small fw-black {{ $utilRate > 80 ? 'text-danger' : ($utilRate > 50 ? 'text-warning' : 'text-success') }}">{{ number_format($utilRate, 1) }}%</span>
                            <div class="progress w-100" style="height: 4px; min-width: 60px;">
                                <div class="progress-bar {{ $utilRate > 80 ? 'bg-danger' : ($utilRate > 50 ? 'bg-warning' : 'bg-success') }}" style="width: {{ $utilRate }}%"></div>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="py-5 text-center text-muted italic small">No inventory data matching report parameters.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<style>
    .fw-black { font-weight: 900; }
    .extra-small { font-size: 0.65rem; }
    .tracking-tighter { letter-spacing: -0.05em; }
</style>
@endsection
