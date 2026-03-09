@extends('layouts.app')
@section('title', 'Circulation Analysis Report')
@section('page-title', 'Reporting Engine')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col-md-6">
        <h2 class="h4 mb-1 fw-black text-dark tracking-tighter uppercase mb-0">
            <i class="bi bi-clock-history me-2 text-primary"></i>EQUIPMENT CIRCULATION
        </h2>
        <p class="text-muted small mb-0">Historical overview of asset borrowing and return dynamics</p>
    </div>
    <div class="col-md-6 text-md-end mt-3 mt-md-0">
        <div class="btn-group shadow-sm">
            <a href="{{ route('reports.export', ['type' => 'borrow'] + request()->all()) }}" class="btn btn-primary fw-bold">
                <i class="bi bi-file-earmark-pdf me-2"></i>PDF Report
            </a>
        </div>
    </div>
</div>

{{-- Circulation Filter --}}
<div class="card border-0 shadow-sm mb-4 overflow-hidden">
    <div class="card-body bg-light bg-opacity-50">
        <form method="GET" action="{{ route('reports.borrow') }}" class="row g-3">
            <div class="col-md-3">
                <label class="form-label extra-small fw-black text-muted text-uppercase tracking-widest">Target Project</label>
                <select name="project_id" class="form-select form-select-sm border-0 shadow-sm">
                    <option value="">All Project Sites</option>
                    @foreach($projects as $p)
                    <option value="{{ $p->id }}" {{ request('project_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label extra-small fw-black text-muted text-uppercase tracking-widest">Timeframe Start</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control form-control-sm border-0 shadow-sm">
            </div>
            <div class="col-md-3">
                <label class="form-label extra-small fw-black text-muted text-uppercase tracking-widest">Status Focus</label>
                <select name="status" class="form-select form-select-sm border-0 shadow-sm">
                    <option value="">All Activity States</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending Approval</option>
                    <option value="borrowed" {{ request('status') === 'borrowed' ? 'selected' : '' }}>Currently Borrowed</option>
                    <option value="returned" {{ request('status') === 'returned' ? 'selected' : '' }}>Returned & Completed</option>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold">APPLY</button>
                <a href="{{ route('reports.borrow') }}" class="btn btn-light btn-sm w-100 border fw-bold">RESET</a>
            </div>
        </form>
    </div>
</div>

{{-- Stats Cards --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100 border-start border-primary border-4">
            <div class="card-body p-3">
                <small class="text-muted extra-small fw-black text-uppercase tracking-widest d-block mb-1">Total Transactions</small>
                <h4 class="fw-black text-dark mb-0">{{ $borrows->total() }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100 border-start border-info border-4">
            <div class="card-body p-3">
                <small class="text-muted extra-small fw-black text-uppercase tracking-widest d-block mb-1">Items Out</small>
                <h4 class="fw-black text-info mb-0">{{ $borrows->where('status', 'borrowed')->count() }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100 border-start border-success border-4">
            <div class="card-body p-3">
                <small class="text-muted extra-small fw-black text-uppercase tracking-widest d-block mb-1">Success Rate</small>
                @php $rate = $borrows->total() > 0 ? ($borrows->where('status', 'returned')->count() / $borrows->total()) * 100 : 0; @endphp
                <h4 class="fw-black text-success mb-0">{{ number_format($rate, 1) }}%</h4>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100 border-start border-danger border-4">
            <div class="card-body p-3">
                <small class="text-muted extra-small fw-black text-uppercase tracking-widest d-block mb-1">Overdue items</small>
                <h4 class="fw-black text-danger mb-0">{{ $borrows->where('is_overdue', true)->count() }}</h4>
            </div>
        </div>
    </div>
</div>

{{-- Circulation Table --}}
<div class="card border-0 shadow-sm overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr class="extra-small text-muted text-uppercase fw-black tracking-widest">
                    <th class="ps-4">Code / Date</th>
                    <th>Project Site</th>
                    <th>PIC / Borrower</th>
                    <th>Equipment</th>
                    <th class="text-center">Initial Due</th>
                    <th class="text-center">Status</th>
                    <th class="text-end pe-4">Timing</th>
                </tr>
            </thead>
            <tbody class="border-top-0">
                @forelse($borrows as $borrow)
                <tr>
                    <td class="ps-4 py-3">
                        <div class="fw-bold text-dark small text-uppercase">#{{ $borrow->code }}</div>
                        <div class="extra-small text-muted">{{ $borrow->created_at->format('d M Y, H:i') }}</div>
                    </td>
                    <td>
                        <div class="small fw-bold text-dark text-uppercase">{{ $borrow->project->name }}</div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($borrow->requester->name) }}&size=24&background=4e73df&color=fff" class="rounded-circle shadow-sm">
                            <span class="small fw-semibold text-dark">{{ $borrow->requester->name }}</span>
                        </div>
                    </td>
                    <td>
                        @foreach($borrow->items as $item)
                            <div class="extra-small fw-bold text-dark">• {{ $item->inventory->name }} ({{ $item->quantity }})</div>
                        @endforeach
                    </td>
                    <td class="text-center small fw-bold font-monospace">{{ $borrow->expected_return_date->format('d/m/Y') }}</td>
                    <td class="text-center">
                        @php
                            $stClass = match($borrow->status) {
                                'pending' => 'bg-warning text-dark',
                                'borrowed' => 'bg-primary',
                                'returned' => 'bg-success',
                                'rejected' => 'bg-danger',
                                default => 'bg-secondary'
                            };
                        @endphp
                        <span class="badge {{ $stClass }} px-2 py-1 extra-small uppercase fw-black rounded-pill">
                            {{ $borrow->status }}
                        </span>
                    </td>
                    <td class="text-end pe-4">
                        @if($borrow->status === 'returned')
                            <span class="badge bg-success-subtle text-success border border-success extra-small fw-black uppercase">Completed</span>
                        @elseif($borrow->is_overdue)
                            <span class="badge bg-danger-subtle text-danger border border-danger extra-small fw-black uppercase animate-pulse">{{ $borrow->expected_return_date->diffInDays(now()) }}d LATE</span>
                        @else
                            <span class="badge bg-light text-muted border extra-small fw-black uppercase">{{ $borrow->expected_return_date->diffInDays(now()) }}d Left</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="py-5 text-center text-muted italic small">No circulation history matching selected parameters.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    {{ $borrows->links('pagination::bootstrap-5') }}
</div>

<style>
    .fw-black { font-weight: 900; }
    .extra-small { font-size: 0.65rem; }
    .tracking-tighter { letter-spacing: -0.05em; }
</style>
@endsection
