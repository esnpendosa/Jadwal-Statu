@extends('layouts.app')
@section('title', $project->name)
@section('page-title', __('projects.title'))

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('projects.index') }}" class="btn btn-light border shadow-sm btn-sm">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h2 class="h4 mb-1 fw-bold text-dark">{{ $project->name }}</h2>
            <div class="d-flex align-items-center gap-2">
                <code class="small text-muted bg-light px-2 rounded">{{ $project->code }}</code>
                <span class="badge {{ $project->status_badge_class }} rounded-pill px-3 py-1 extra-small">
                    {{ strtoupper(__('projects.statuses.' . $project->status)) }}
                </span>
                <span class="badge {{ $project->risk_badge_class }} rounded-pill px-3 py-1 extra-small">
                    RISK: {{ strtoupper(__('projects.risk_levels.' . ($project->risk_level ?? 'low'))) }}
                </span>
            </div>
        </div>
    </div>
    <div class="d-flex gap-2">
        @can('edit project')
        <a href="{{ route('projects.edit', $project) }}" class="btn btn-light border btn-sm px-3 fw-bold">
            <i class="bi bi-pencil me-1"></i> {{ __('common.edit') }}
        </a>
        @endcan
        @can('create borrow')
        <a href="{{ route('borrow.create', ['project_id' => $project->id]) }}" class="btn btn-primary btn-sm px-4 fw-bold shadow-sm">
            <i class="bi bi-cart-plus me-1"></i> Borrow Items
        </a>
        @endcan
    </div>
</div>

<div class="row g-4">
    {{-- Main Content --}}
    <div class="col-xl-8">
        {{-- Counter Cards --}}
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm overflow-hidden h-100">
                    <div class="card-body p-4 d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-3 me-3">
                            <i class="bi bi-box-seam fs-3"></i>
                        </div>
                        <div>
                            <small class="text-uppercase text-muted fw-bold extra-small tracking-wider d-block mb-1">Active Borrows</small>
                            <h4 class="fw-black mb-0">{{ $project->borrows()->where('status', 'borrowed')->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm overflow-hidden h-100">
                    <div class="card-body p-4 d-flex align-items-center">
                        <div class="bg-success bg-opacity-10 text-success rounded-circle p-3 me-3">
                            <i class="bi bi-check2-circle fs-3"></i>
                        </div>
                        <div>
                            <small class="text-uppercase text-muted fw-bold extra-small tracking-wider d-block mb-1">Completed</small>
                            <h4 class="fw-black mb-0">{{ $project->borrows()->where('status', 'returned')->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm overflow-hidden h-100">
                    <div class="card-body p-4 d-flex align-items-center">
                        <div class="bg-danger bg-opacity-10 text-danger rounded-circle p-3 me-3">
                            <i class="bi bi-exclamation-octagon fs-3"></i>
                        </div>
                        <div>
                            <small class="text-uppercase text-muted fw-bold extra-small tracking-wider d-block mb-1">Overdue</small>
                            <h4 class="fw-black mb-0">{{ $project->borrows()->where('status', 'borrowed')->where('expected_return_date', '<', now())->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Active Items Table --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-dark text-uppercase small tracking-wider">Currently Borrowed Items</h6>
                <div class="badge bg-light text-dark border extra-small">LIVE INVENTORY</div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr class="extra-small text-muted text-uppercase">
                            <th class="ps-4">Inventory Item</th>
                            <th>Code</th>
                            <th class="text-center">Quantity</th>
                            <th>Deadline</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($project->borrows()->where('status', 'borrowed')->with('items.inventory')->latest()->get() as $borrow)
                        <tr>
                            <td class="ps-4 py-3">
                                @php $firstItem = $borrow->items->first(); @endphp
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-white border rounded p-1" style="width: 40px; height: 40px; overflow: hidden;">
                                        @if($firstItem?->inventory?->image)
                                            <img src="{{ asset('storage/' . $firstItem->inventory->image) }}" class="img-fluid w-100 h-100 object-fit-cover">
                                        @else
                                            <i class="bi bi-box text-muted opacity-25 d-block text-center mt-1"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark small">
                                            {{ $firstItem?->inventory?->name ?? 'Unknown' }}
                                            @if($borrow->items->count() > 1)
                                                <small class="text-primary fw-bold">+{{ $borrow->items->count() - 1 }} More</small>
                                            @endif
                                        </div>
                                        <div class="extra-small text-muted text-uppercase">{{ $firstItem?->inventory?->category?->name ?? '—' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="font-monospace small text-muted">{{ $borrow->code }}</td>
                            <td class="text-center fw-bold text-primary">{{ $borrow->total_quantity }} Units</td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="small {{ $borrow->is_overdue ? 'text-danger fw-black' : 'text-dark fw-semibold' }}">
                                        {{ $borrow->expected_return_date->translatedFormat('d M Y') }}
                                    </span>
                                    @if($borrow->is_overdue)
                                    <small class="text-danger extra-small fw-bold">LATE BY {{ $borrow->expected_return_date->diffInDays(now()) }} DAYS</small>
                                    @endif
                                </div>
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('borrow.show', $borrow) }}" class="btn btn-sm btn-light border-0 shadow-none"><i class="bi bi-eye"></i></a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-5 text-center text-muted small">No active borrowings found for this project.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Activity Timeline --}}
        @if($project->borrows()->where('status', 'returned')->count() > 0)
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="mb-0 fw-bold text-dark text-uppercase small tracking-wider">Recent Returns Activity</h6>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @foreach($project->borrows()->where('status', 'returned')->with(['items.inventory', 'returnTransactions'])->latest()->take(5)->get() as $activity)
                        @foreach($activity->returnTransactions as $ret)
                        <div class="list-group-item px-4 py-3 border-0 border-bottom">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-success bg-opacity-10 text-success rounded-circle p-2">
                                        <i class="bi bi-arrow-return-left"></i>
                                    </div>
                                    <div>
                                        <p class="small text-dark mb-0">
                                            Returned <strong>{{ $ret->quantity_returned }}</strong> units of <strong>{{ $ret->borrowItem->inventory?->name }}</strong>
                                        </p>
                                        <small class="extra-small text-muted uppercase">{{ $ret->created_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                                @if($ret->condition_status !== 'good')
                                <span class="badge bg-danger border border-danger bg-opacity-10 text-danger extra-small">{{ strtoupper($ret->condition_status) }}</span>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- Sidebar --}}
    <div class="col-xl-4">
        {{-- Personnel --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <h6 class="mb-0 fw-bold text-dark text-uppercase small tracking-wider">Project Team</h6>
            </div>
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($project->manager?->name ?? 'U') }}&background=6366f1&color=fff&size=40" class="rounded-circle shadow-sm">
                    <div>
                        <small class="text-muted text-uppercase extra-small fw-bold d-block mb-1">Project Manager</small>
                        <h6 class="fw-bold text-dark mb-0 small">{{ $project->manager?->name ?: 'N/A' }}</h6>
                        <small class="extra-small text-muted">Manual Input</small>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-3 pt-3 border-top">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($project->pic?->name ?? 'U') }}&background=8b5cf6&color=fff&size=40" class="rounded-circle shadow-sm">
                    <div>
                        <small class="text-muted text-uppercase extra-small fw-bold d-block mb-1">Project PIC</small>
                        <h6 class="fw-bold text-dark mb-0 small">{{ $project->pic?->name ?: 'N/A' }}</h6>
                        <small class="extra-small text-muted">{{ $project->pic?->email ?: '' }}</small>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-light border-0 p-4 pt-0">
                <div class="row g-3">
                    <div class="col-6">
                        <small class="text-muted text-uppercase extra-small fw-bold d-block mb-1">Start Date</small>
                        <span class="small fw-bold text-dark">{{ $project->start_date->translatedFormat('d M Y') }}</span>
                    </div>
                    <div class="col-6">
                        <small class="text-muted text-uppercase extra-small fw-bold d-block mb-1">End Date</small>
                        <span class="small fw-bold text-dark">{{ $project->end_date->translatedFormat('d M Y') }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- AI Insights --}}
        <div class="card border-0 shadow-sm overflow-hidden mb-4">
            <div class="card-header bg-indigo border-0 py-3 d-flex align-items-center gap-2">
                <i class="bi bi-lightning-charge-fill text-warning"></i>
                <h6 class="mb-0 fw-bold text-white small text-uppercase tracking-wider">AI Project Risk Analysis</h6>
            </div>
            <div class="card-body p-4">
                @forelse($project->aiSuggestions()->latest()->take(3)->get() as $suggestion)
                <div class="alert {{ $suggestion->severity === 'critical' ? 'alert-danger shadow-sm border-0 mb-3 bg-danger bg-opacity-10' : ($suggestion->severity === 'warning' ? 'alert-warning shadow-sm border-0 mb-3 bg-warning bg-opacity-10' : 'alert-info shadow-sm border-0 mb-3 bg-info bg-opacity-10') }} p-3">
                    <div class="d-flex justify-content-between mb-1">
                        <small class="fw-black extra-small text-uppercase {{ $suggestion->severity === 'critical' ? 'text-danger' : ($suggestion->severity === 'warning' ? 'text-warning' : 'text-info') }}">
                            {{ strtoupper($suggestion->severity) }} ALERT
                        </small>
                        <small class="extra-small text-muted">{{ $suggestion->created_at->diffForHumans() }}</small>
                    </div>
                    <p class="extra-small text-dark mb-0 opacity-75">"{{ $suggestion->getSuggestionForLocale() }}"</p>
                </div>
                @empty
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-shield-check fs-1 opacity-25 d-block mb-2"></i>
                    <p class="extra-small mb-0">No risk factors detected by AI currently.</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Location --}}
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <small class="text-muted text-uppercase extra-small fw-bold d-block mb-2">Project Location</small>
                <div class="d-flex gap-3 align-items-start">
                    <div class="bg-light rounded p-2 text-primary"><i class="bi bi-geo-alt fs-4"></i></div>
                    <p class="small fw-bold text-dark mb-0 mt-1">{{ $project->location ?: 'No location set' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-indigo { background: #6366f1; }
    .fw-black { font-weight: 900; }
    .extra-small { font-size: 0.65rem; }
    .leading-none { line-height: 1; }
</style>
@endsection
