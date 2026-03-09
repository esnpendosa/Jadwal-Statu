@extends('layouts.app')
@section('title', 'Audit Logs')
@section('page-title', 'Admin Panel')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h2 class="h4 mb-1 fw-bold text-dark text-uppercase tracking-tight">System Transparency</h2>
        <p class="text-muted small mb-0">Historical record of all data modifications and system events</p>
    </div>
</div>

{{-- Log Filters --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <form method="GET" action="{{ route('admin.audit-logs.index') }}" class="row g-3">
            <div class="col-md-3">
                <label class="form-label extra-small fw-bold text-muted text-uppercase">Triggered By</label>
                <select name="user_id" class="form-select @if(request('user_id')) border-primary @endif">
                    <option value="">All Personnel</option>
                    @foreach($users as $u)
                    <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label extra-small fw-bold text-muted text-uppercase">Activity Type</label>
                <select name="event" class="form-select @if(request('event')) border-primary @endif">
                    <option value="">All Events</option>
                    @foreach(['created', 'updated', 'deleted'] as $ev)
                    <option value="{{ $ev }}" {{ request('event') === $ev ? 'selected' : '' }}>{{ strtoupper($ev) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label extra-small fw-bold text-muted text-uppercase">Affected Module</label>
                <input type="text" name="module" value="{{ request('module') }}" placeholder="e.g. Inventory, Borrow" class="form-control @if(request('module')) border-primary @endif">
            </div>
            <div class="col-md-3 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1 fw-bold extra-small text-uppercase shadow-sm">
                    <i class="bi bi-search me-1"></i> {{ __('common.search') }}
                </button>
                <a href="{{ route('admin.audit-logs.index') }}" class="btn btn-light border fw-bold extra-small text-uppercase">
                    RESET
                </a>
            </div>
        </form>
    </div>
</div>

{{-- Logs List --}}
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr class="extra-small text-muted text-uppercase fw-bold">
                    <th class="ps-4 py-3">Timestamp</th>
                    <th class="py-3">Personnel</th>
                    <th class="py-3">Action</th>
                    <th class="py-3">Impacted Resource</th>
                    <th class="py-3">IP Address</th>
                    <th class="text-end pe-4 py-3">Detail</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td class="ps-4">
                        <div class="d-flex flex-column">
                            <span class="text-dark small fw-bold">{{ $log->created_at->format('d M Y') }}</span>
                            <span class="extra-small text-muted font-monospace">{{ $log->created_at->format('H:i:s') }}</span>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($log->user?->name ?: 'System') }}&background=f8f9fc&color=4e73df&size=24&bold=true" class="rounded-circle shadow-sm">
                            <span class="small fw-semibold text-dark">{{ $log->user?->name ?: 'System Process' }}</span>
                        </div>
                    </td>
                    <td>
                        <span class="badge {{ $log->event === 'created' ? 'bg-success-subtle text-success border border-success-subtle' : ($log->event === 'deleted' ? 'bg-danger-subtle text-danger border border-danger-subtle' : 'bg-warning-subtle text-warning border border-warning-subtle') }} extra-small fw-bold">
                            {{ strtoupper($log->event) }}
                        </span>
                    </td>
                    <td>
                        <div class="d-flex flex-column">
                            <span class="small fw-bold text-dark uppercase tracking-tighter">{{ class_basename($log->auditable_type) }}</span>
                            <span class="extra-small text-muted font-monospace">ID: {{ $log->auditable_id }}</span>
                        </div>
                    </td>
                    <td>
                        <code class="extra-small text-muted">{{ $log->ip_address }}</code>
                    </td>
                    <td class="text-end pe-4">
                        <button onclick="viewAuditDetail({{ $log->id }})" class="btn btn-light btn-sm border-0 shadow-none text-primary">
                            <i class="bi bi-info-circle"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-5 text-center text-muted">No activity logs recorded.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($logs->hasPages())
    <div class="card-footer bg-white border-0 py-3">
        {{ $logs->links() }}
    </div>
    @endif
</div>

{{-- Detail Modal --}}
<div class="modal fade" id="auditModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-dark text-white border-0 py-3">
                <h5 class="modal-title h6 fw-bold text-uppercase tracking-wider">Audit Investigation Data</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" id="modal-content">
                {{-- Spinner --}}
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
            </div>
            <div class="modal-footer border-0 bg-light p-3">
                <button type="button" class="btn btn-dark btn-sm px-4 fw-bold extra-small text-uppercase" data-bs-dismiss="modal">Close Inspector</button>
            </div>
        </div>
    </div>
</div>

<style>
    .extra-small { font-size: 0.65rem; }
    .bg-success-subtle { background-color: rgba(25, 135, 84, 0.1); }
    .bg-warning-subtle { background-color: rgba(255, 193, 7, 0.1); }
    .bg-danger-subtle { background-color: rgba(220, 53, 69, 0.1); }
</style>
@endsection

@push('scripts')
<script>
const auditModal = new bootstrap.Modal(document.getElementById('auditModal'));

function viewAuditDetail(id) {
    const content = document.getElementById('modal-content');
    content.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div></div>';
    auditModal.show();

    fetch(`/admin/audit-logs/${id}`)
        .then(res => res.json())
        .then(data => {
            content.innerHTML = `
                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <small class="text-muted extra-small fw-bold text-uppercase d-block mb-1">User Agent Identity</small>
                        <div class="p-2 bg-light rounded small text-dark border">${data.user_agent}</div>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted extra-small fw-bold text-uppercase d-block mb-1">Impacted Endpoint</small>
                        <div class="p-2 bg-light rounded small font-monospace text-primary border text-truncate">${data.url}</div>
                    </div>
                </div>
                <h6 class="extra-small fw-black text-dark text-uppercase mb-3 tracking-widest border-bottom pb-2">Payload Comparison Analysis</h6>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="card bg-danger bg-opacity-10 border-danger border-opacity-25 h-100">
                            <div class="card-header bg-transparent border-0 py-2">
                                <small class="text-danger fw-black extra-small text-uppercase">Old System Attributes</small>
                            </div>
                            <div class="card-body p-2 pt-0">
                                <pre class="font-monospace extra-small bg-white p-2 rounded border mb-0" style="max-height: 300px; overflow-y: auto;">${JSON.stringify(data.old_values, null, 2)}</pre>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-success bg-opacity-10 border-success border-opacity-25 h-100">
                            <div class="card-header bg-transparent border-0 py-2">
                                <small class="text-success fw-black extra-small text-uppercase">New System Attributes</small>
                            </div>
                            <div class="card-body p-2 pt-0">
                                <pre class="font-monospace extra-small bg-white p-2 rounded border mb-0" style="max-height: 300px; overflow-y: auto;">${JSON.stringify(data.new_values, null, 2)}</pre>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
}
</script>
@endpush
