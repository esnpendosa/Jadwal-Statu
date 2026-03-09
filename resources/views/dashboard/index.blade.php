@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="row">
    <!-- Stat Cards -->
    @if($isPic)
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-start border-primary border-4 h-100 py-2 shadow-sm">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">My Asset Holdings</div>
                        <div class="h5 mb-0 font-weight-bold text-dark">{{ number_format($stats['my_holdings']) }} items</div>
                    </div>
                    <div class="col-auto"><i class="bi bi-box-seam fs-2 text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-start border-primary border-4 h-100 py-2 shadow-sm">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Assets</div>
                        <div class="h5 mb-0 font-weight-bold text-dark">{{ number_format($stats['total_inventory']) }}</div>
                    </div>
                    <div class="col-auto"><i class="bi bi-box-seam fs-2 text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-start border-success border-4 h-100 py-2 shadow-sm">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">{{ $isPic ? 'My Assignments' : 'Active Projects' }}</div>
                        <div class="h5 mb-0 font-weight-bold text-dark">{{ number_format($stats['total_projects']) }}</div>
                    </div>
                    <div class="col-auto"><i class="bi bi-layers fs-2 text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-start border-danger border-4 h-100 py-2 shadow-sm">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Overdue Returns</div>
                        <div class="h5 mb-0 font-weight-bold text-dark">{{ $overdueCount }}</div>
                    </div>
                    <div class="col-auto"><i class="bi bi-exclamation-octagon fs-2 text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-start border-warning border-4 h-100 py-2 shadow-sm">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Low Stock Alerts</div>
                        <div class="h5 mb-0 font-weight-bold text-dark">{{ number_format($stats['low_stock']) }}</div>
                    </div>
                    <div class="col-auto"><i class="bi bi-shield-exclamation fs-2 text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Activity Chart -->
    <div class="col-xl-8 col-lg-7">
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-header d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold">Distribution Overview</h6>
            </div>
            <div class="card-body">
                <div class="chart-area" style="height: 320px">
                    <canvas id="monthlyChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- AI Insights Area -->
    <div class="col-xl-4 col-lg-5">
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-header d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">AI Predictive Panel</h6>
            </div>
            <div class="card-body" style="max-height: 360px; overflow-y: auto">
                @forelse($aiInsights['active_suggestions'] ?? [] as $suggestion)
                <div class="mb-3 p-3 border rounded-3 bg-light">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <small class="text-primary font-weight-bold">{{ strtoupper($suggestion->target_label ?? 'OPTIMIZATION') }}</small>
                        @if(($suggestion->severity ?? '') === 'critical')
                        <span class="badge bg-danger rounded-pill">Critical</span>
                        @endif
                    </div>
                    <p class="small mb-0">{{ $suggestion->getSuggestionForLocale() }}</p>
                </div>
                @empty
                <div class="text-center py-5">
                    <i class="bi bi-cpu fs-1 text-gray-200"></i>
                    <p class="text-muted small mt-2">No critical insights at this moment.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Role Specific Tables -->
    <div class="col-lg-6">
        @if($isAdmin)
        <div class="card shadow-sm border-0 mb-4 h-100">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <h6 class="m-0 fw-black text-dark tracking-tighter uppercase"><i class="bi bi-box-arrow-in-down me-2 text-warning"></i> PENDING RETURNS</h6>
                <span class="badge bg-warning rounded-pill px-3">{{ count($roleData['pending_returns'] ?? []) }} TO PROCESS</span>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <tbody class="border-top-0">
                        @forelse($roleData['pending_returns'] ?? [] as $return)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-dark small">{{ $return->borrowItem?->inventory?->name }}</div>
                                <div class="extra-small text-muted font-monospace">TRX: #{{ $return->code }}</div>
                            </td>
                            <td>
                                <div class="extra-small fw-bold text-dark">FROM PIC:</div>
                                <div class="extra-small text-muted">{{ $return->returnedBy?->name }}</div>
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('return.show', $return) }}" class="btn btn-sm btn-dark rounded-pill extra-small fw-black px-3">PROCESS</a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center py-5 text-muted small italic">No pending returns to process.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        @if($isPic)
        <div class="card shadow-sm border-0 mb-4 h-100">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="m-0 fw-black text-dark tracking-tighter uppercase"><i class="bi bi-clock-history me-2 text-primary"></i> PENDING MY REQUESTS</h6>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <tbody class="border-top-0">
                        @forelse($roleData['my_pending_borrows'] ?? [] as $borrow)
                        <tr>
                            <td class="ps-4">
                                <div class="font-monospace text-primary extra-small fw-bold mb-1">#{{ $borrow->code }}</div>
                                <div class="small fw-bold text-dark text-uppercase">{{ $borrow->items->first()?->inventory?->name ?? 'Mixed Items' }}</div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-warning-subtle text-warning extra-small rounded-pill px-3 py-1">AWAITING APPROVAL</span>
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('borrow.show', $borrow) }}" class="btn btn-sm btn-light border extra-small px-3">DETAILS</a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center py-5 text-muted small italic">No pending borrow requests.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>

    <div class="col-lg-6">
        @if($isAdmin)
        <div class="card shadow-sm border-0 mb-4 h-100">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <h6 class="m-0 fw-black text-dark tracking-tighter uppercase"><i class="bi bi-shield-check me-2 text-primary"></i> APPROVAL QUEUE</h6>
                <span class="badge bg-primary rounded-pill px-3">{{ count($roleData['pending_approvals'] ?? []) }} PENDING</span>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <tbody class="border-top-0">
                        @forelse($roleData['pending_approvals'] ?? [] as $approval)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-dark small text-uppercase">{{ $approval->requester?->name }}</div>
                                <div class="extra-small text-muted">PROJECT: {{ $approval->project?->name ?? 'N/A' }}</div>
                            </td>
                            <td>
                                <div class="extra-small fw-bold text-muted text-uppercase">TOTAL ITEMS:</div>
                                <div class="small fw-black text-dark">{{ $approval->items->sum('quantity') }}</div>
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('borrow.show', $approval) }}" class="btn btn-sm btn-primary rounded-pill extra-small fw-black px-3">APPROVE / REJECT</a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center py-5 text-muted small italic">Approval queue is empty.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>

    <!-- AI SMART INSIGHTS SECTION -->
    <div class="col-12 mb-4">
        @if($isAdmin)
        <div class="card shadow-sm border-0 bg-primary bg-opacity-10" style="border-left: 5px solid #4e73df !important;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h5 class="m-0 fw-black text-primary"><i class="bi bi-robot me-2"></i> AI SMART INSIGHTS & RECOMMENDATIONS</h5>
                    <div class="d-flex gap-2">
                        <form action="{{ route('dashboard.ai-sync') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3 fw-bold shadow-sm">
                                <i class="bi bi-arrow-repeat me-1"></i> REFRESH AI ANALYSIS
                            </button>
                        </form>
                        <span class="badge bg-primary rounded-pill px-3 d-flex align-items-center">{{ count($aiInsights['active_suggestions'] ?? []) }} NEW ALERTS</span>
                    </div>
                </div>
                
                <div class="row g-4">
                    @forelse($aiInsights['active_suggestions'] ?? [] as $suggestion)
                    <div class="col-md-6 col-xl-4">
                        <div class="bg-white p-3 rounded-4 shadow-sm h-100 border border-info border-opacity-10 position-relative hover-lift transition-all">
                            @php
                                $sevClass = match($suggestion->severity) {
                                    'critical' => 'text-danger',
                                    'warning'  => 'text-warning',
                                    default    => 'text-info',
                                };
                                $sevBg = match($suggestion->severity) {
                                    'critical' => 'bg-danger-subtle',
                                    'warning'  => 'bg-warning-subtle',
                                    default    => 'bg-info-subtle',
                                };
                            @endphp
                            <div class="d-flex align-items-start gap-3">
                                <div class="{{ $sevBg }} {{ $sevClass }} p-2 rounded-3">
                                    <i class="bi bi-lightning-charge-fill"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="extra-small fw-black text-uppercase tracking-widest text-muted">{{ $suggestion->target_type }}</span>
                                        <span class="extra-small text-muted">{{ $suggestion->generated_at->diffForHumans() }}</span>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-2">{{ $suggestion->target_label }}</h6>
                                    <p class="small text-secondary mb-0 line-clamp-2">
                                        {{ $suggestion->getSuggestionForLocale() }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-12 text-center py-4">
                        <div class="mb-2 opacity-50"><i class="bi bi-check2-circle fs-1 text-success"></i></div>
                        <h6 class="fw-bold text-muted">AI System: Everything looks stable!</h6>
                        <p class="extra-small text-muted mb-0 uppercase tracking-widest">No immediate recommendations at this time.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="col-12 mt-4">
        @if($isAdmin || $isPic)
        <div class="card shadow-sm border-0 border-top border-primary border-4">
            <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div>
                    <h6 class="m-0 fw-bold text-primary"><i class="bi bi-collection-fill me-2"></i> SEMUA DATA TRANSAKSI</h6>
                    <small class="text-muted extra-small fw-bold">TOTAL REGISTERED: {{ count($roleData['active_borrow_transactions'] ?? []) }} RECORDS</small>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('borrow.create') }}" class="btn btn-primary btn-sm fw-black extra-small px-3">
                        <i class="bi bi-plus-lg me-1"></i> NEW BORROW
                    </a>
                    <a href="{{ route('borrow.index') }}" class="btn btn-light btn-sm border fw-black extra-small px-3">
                        VIEW ALL <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
            <div class="table-responsive" style="max-height: 550px; overflow-y: auto; scrollbar-width: thin;">
                <table class="table align-middle table-hover mb-0">
                    <thead class="bg-light sticky-top shadow-sm" style="z-index: 10;">
                        <tr class="extra-small text-muted text-uppercase fw-black tracking-widest">
                            <th class="ps-4 border-bottom-0">ID / Item Details</th>
                            <th class="border-bottom-0">PIC / Peminjam</th>
                            <th class="border-bottom-0">Project</th>
                            <th class="text-center border-bottom-0">Qty</th>
                            <th class="border-bottom-0">Dates</th>
                            <th class="text-center border-bottom-0">Status</th>
                            <th class="text-end pe-4 border-bottom-0">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse($roleData['active_borrow_transactions'] ?? [] as $trx)
                        @php
                            $stBadge = match($trx->status) {
                                'pending'   => 'bg-warning text-dark border-0',
                                'borrowed'  => 'bg-primary text-white border-0',
                                'rejected'  => 'bg-danger text-white border-0',
                                'completed', 'returned' => 'bg-success text-white border-0',
                                default     => 'bg-secondary text-white border-0',
                            };
                            $firstItem = $trx->items->first();
                            $itemCount = $trx->items->count();
                            $isOverdue = $trx->is_overdue && !in_array($trx->status, ['completed','rejected','returned']);
                        @endphp
                        <tr>
                            <td class="ps-4 py-3">
                                <div class="font-monospace text-primary fw-bold mb-1" style="font-size: 0.75rem;">#{{ $trx->code }}</div>
                                @if($firstItem)
                                    <div class="small fw-bold text-dark text-uppercase">{{ $firstItem->inventory?->name ?? '-' }}</div>
                                    @if($itemCount > 1)
                                        <div class="extra-small text-muted mt-1">+{{ $itemCount - 1 }} additional items</div>
                                    @endif
                                @else
                                    <span class="text-muted small italic">—</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="position-relative">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($trx->requester?->name ?? 'U') }}&background=4e73df&color=fff&size=32&bold=true" class="rounded-circle shadow-sm" width="32" height="32">
                                        @if($trx->status === 'borrowed')
                                            <span class="position-absolute bottom-0 end-0 bg-success border border-white border-2 rounded-circle" style="width: 10px; height: 10px;"></span>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="small fw-bold text-dark">{{ $trx->requester?->name ?? '—' }}</div>
                                        <div class="extra-small text-muted font-monospace">{{ $trx->requester?->email ?? '' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="small fw-bold text-dark mb-0">{{ $trx->project?->name ?? 'No Project' }}</div>
                                <div class="extra-small text-muted text-uppercase">{{ $trx->project?->client_name ?? '' }}</div>
                            </td>
                            <td class="text-center">
                                <div class="bg-primary bg-opacity-10 rounded-pill px-2 py-1 d-inline-block">
                                    <span class="fw-black text-primary" style="font-size: 0.7rem;">{{ $trx->items->sum('quantity') }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column gap-1">
                                    <div class="extra-small text-muted d-flex align-items-center gap-1">
                                        <i class="bi bi-calendar-check text-success"></i> {{ $trx->borrow_date ? \Carbon\Carbon::parse($trx->borrow_date)->format('d/m/Y') : '—' }}
                                    </div>
                                    <div class="extra-small {{ $isOverdue ? 'text-danger fw-black' : 'text-muted' }} d-flex align-items-center gap-1">
                                        <i class="bi bi-calendar-x {{ $isOverdue ? 'text-danger' : 'text-primary' }}"></i> 
                                        {{ $trx->expected_return_date->format('d/m/Y') }}
                                        @if($isOverdue) 
                                            <span class="badge bg-danger rounded-circle p-1" style="width: 6px; height: 6px;"></span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $stBadge }} extra-small fw-black rounded-pill px-3 py-1 shadow-sm">{{ strtoupper($trx->status) }}</span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group shadow-sm rounded-3 overflow-hidden">
                                    <a href="{{ route('borrow.show', $trx) }}" class="btn btn-sm btn-white border border-end-0 px-2" title="Detail View">
                                        <i class="bi bi-eye-fill text-primary"></i>
                                    </a>
                                    @if($trx->status === 'borrowed')
                                    <form action="{{ route('borrow.notify', $trx) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-white border border-start-0 px-2" title="Send Email Reminder" onclick="return confirm('Send notification to {{ $trx->requester?->email }}?')">
                                            <i class="bi bi-envelope-fill text-info"></i>
                                        </button>
                                    </form>
                                    @endif
                                    <a href="{{ route('borrow.print', $trx) }}" class="btn btn-sm btn-white border px-2 border-start-0" title="Print Note">
                                        <i class="bi bi-printer-fill text-secondary"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="py-5 text-center bg-light">
                                <div class="mb-3 opacity-25">
                                    <i class="bi bi-search" style="font-size: 3rem;"></i>
                                </div>
                                <h6 class="fw-bold text-muted mb-1 text-uppercase tracking-wider">No Transactions Found</h6>
                                <p class="text-muted extra-small mb-0">Record is empty or filters returned no results.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white border-0 text-center py-3 border-top">
                <p class="mb-0 extra-small fw-black text-muted text-uppercase tracking-widest">End of transaction record</p>
            </div>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('monthlyChart').getContext('2d');
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($monthlyChart['labels']) !!},
                datasets: [{
                    label: 'Peminjaman',
                    data: {!! json_encode($monthlyChart['data']) !!},
                    borderColor: '#4e73df',
                    backgroundColor: 'rgba(78, 115, 223, 0.05)',
                    borderWidth: 3,
                    pointRadius: 3,
                    pointBackgroundColor: "#4e73df",
                    pointBorderColor: "#4e73df",
                    pointHoverRadius: 3,
                    pointHoverBackgroundColor: "#4e73df",
                    pointHoverBorderColor: "#4e73df",
                    pointHitRadius: 10,
                    pointBorderWidth: 2,
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                maintainAspectRatio: false,
                layout: {
                    padding: { left: 10, right: 25, top: 25, bottom: 0 }
                },
                scales: {
                    x: {
                        grid: { display: false, drawBorder: false },
                        ticks: { maxTicksLimit: 7 }
                    },
                    y: {
                        ticks: {
                            maxTicksLimit: 5,
                            padding: 10
                        },
                        grid: {
                            color: "rgb(234, 236, 244)",
                            zeroLineColor: "rgb(234, 236, 244)",
                            drawBorder: false,
                            borderDash: [2],
                            zeroLineBorderDash: [2]
                        }
                    }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
    });
</script>
@endpush
@endsection
