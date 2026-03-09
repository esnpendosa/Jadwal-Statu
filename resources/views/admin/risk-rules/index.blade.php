@extends('layouts.app')
@section('title', 'Risk Scoring Rules')
@section('page-title', 'Admin Panel')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h2 class="h4 mb-1 fw-bold text-dark text-uppercase tracking-tight">Risk Scoring System</h2>
        <p class="text-muted small mb-0">Configure logic that calculates personnel risk levels based on behavior.</p>
    </div>
    @can('manage risk rules')
    <a href="{{ route('admin.risk-rules.create') }}" class="btn btn-primary d-flex align-items-center gap-2 px-3 fw-bold shadow-sm">
        <i class="bi bi-shield-check"></i>
        <span>Add Risk Rule</span>
    </a>
    @endcan
</div>

<div class="card border-0 shadow-sm overflow-hidden mb-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr class="extra-small text-muted text-uppercase fw-bold">
                    <th class="ps-4 py-3">Rule Identity</th>
                    <th class="py-3">Condition Code</th>
                    <th class="py-3">Impact (Points)</th>
                    <th class="text-center py-3">Status</th>
                    <th class="text-end pe-4 py-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rules as $rule)
                <tr class="small">
                    <td class="ps-4">
                        <div class="d-flex flex-column">
                            <span class="fw-bold text-dark">{{ $rule->name }}</span>
                            <span class="extra-small text-muted italic">{{ $rule->description }}</span>
                        </div>
                    </td>
                    <td>
                        <code class="extra-small text-muted font-monospace bg-light px-2 py-0.5 rounded">{{ $rule->rule_key }}</code>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <h5 class="mb-0 fw-black {{ $rule->point_impact > 0 ? 'text-danger' : 'text-success' }}">
                                {{ $rule->point_impact > 0 ? '+' : '' }}{{ $rule->point_impact }}
                            </h5>
                            <span class="extra-small fw-bold text-muted uppercase tracking-widest">PTS</span>
                        </div>
                    </td>
                    <td class="text-center">
                        <form action="{{ route('admin.risk-rules.toggle', $rule) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-sm py-0 {{ $rule->is_active ? 'btn-success text-white' : 'btn-light border text-muted' }} extra-small fw-black rounded-pill border-0 shadow-sm px-3">
                                {{ $rule->is_active ? 'ENABLED' : 'DISABLED' }}
                            </button>
                        </form>
                    </td>
                    <td class="text-end pe-4">
                        <div class="d-flex justify-content-end gap-1">
                            <a href="{{ route('admin.risk-rules.edit', $rule) }}" class="btn btn-light btn-sm border-0 shadow-none text-primary">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            <form action="{{ route('admin.risk-rules.destroy', $rule) }}" method="POST" class="d-inline" onsubmit="return confirm('Silahkan hapus aturan risiko ini?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-light btn-sm border-0 shadow-none text-danger">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-5 text-center">
                        <i class="bi bi-shield-slash display-4 text-muted opacity-25 d-block mb-3"></i>
                        <span class="text-muted small fw-bold uppercase tracking-widest">No risk assessment rules defined.</span>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="alert alert-info border-0 shadow-sm d-flex align-items-center gap-3">
    <i class="bi bi-info-circle-fill fs-4"></i>
    <div class="small">
        <strong class="text-uppercase tracking-wider">System Intelligence Note:</strong>
        <p class="mb-0 opacity-75">Risk scores are automatically recalculated upon borrowing and return events. High scores (above 50 threshold) will trigger restricted borrowing permissions for the associated personnel.</p>
    </div>
</div>

<style>
    .extra-small { font-size: 0.65rem; }
    .fw-black { font-weight: 900; }
</style>
@endsection
