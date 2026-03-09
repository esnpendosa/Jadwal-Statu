@extends('layouts.app')
@section('title', 'AI Suggestion Rules')
@section('page-title', 'Admin Panel')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h2 class="h4 mb-1 fw-bold text-dark text-uppercase tracking-tight">AI Logic Framework</h2>
        <p class="text-muted small mb-0">Define rule-based triggers for automated smart suggestions and asset monitoring.</p>
    </div>
    @can('manage ai rules')
    <a href="{{ route('admin.ai-rules.create') }}" class="btn btn-primary d-flex align-items-center gap-2 px-3 fw-bold shadow-sm">
        <i class="bi bi-lightning-charge"></i>
        <span>Add AI Rule</span>
    </a>
    @endcan
</div>

<div class="card border-0 shadow-sm overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr class="extra-small text-muted text-uppercase fw-bold">
                    <th class="ps-4 py-3">Trigger Logic & Key</th>
                    <th class="py-3">Multilingual Suggestions</th>
                    <th class="text-center py-3">Severity</th>
                    <th class="text-center py-3">Deployment</th>
                    <th class="text-end pe-4 py-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rules as $rule)
                <tr class="small">
                    <td class="ps-4">
                        <div class="d-flex flex-column">
                            <span class="fw-bold text-dark">{{ $rule->name }}</span>
                            <code class="extra-small text-primary font-monospace bg-primary bg-opacity-10 px-2 py-0.5 rounded w-fit mt-1">{{ $rule->rule_key }}</code>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex flex-column gap-1">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-light text-muted extra-small border">ID</span>
                                <span class="extra-small text-truncate opacity-75" style="max-width: 300px;">{{ $rule->suggestion_id }}</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-light text-muted extra-small border">EN</span>
                                <span class="extra-small text-truncate opacity-75" style="max-width: 300px;">{{ $rule->suggestion_en }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="text-center">
                        <span class="badge {{ $rule->severity === 'critical' ? 'bg-danger' : ($rule->severity === 'warning' ? 'bg-warning text-dark' : 'bg-info') }} extra-small fw-black px-3 py-1">
                            {{ strtoupper($rule->severity) }}
                        </span>
                    </td>
                    <td class="text-center">
                        <form action="{{ route('admin.ai-rules.toggle', $rule) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-sm py-0 {{ $rule->is_active ? 'btn-success text-white' : 'btn-light border text-muted' }} extra-small fw-black rounded-pill border-0 shadow-sm px-3 mt-1">
                                {{ $rule->is_active ? 'LIVE' : 'IDLE' }}
                            </button>
                        </form>
                    </td>
                    <td class="text-end pe-4">
                        <div class="d-flex justify-content-end gap-1">
                            <a href="{{ route('admin.ai-rules.edit', $rule) }}" class="btn btn-light btn-sm border-0 shadow-none text-primary">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            <form action="{{ route('admin.ai-rules.destroy', $rule) }}" method="POST" class="d-inline" onsubmit="return confirm('Deleting this AI logic will remove future automated insights based on this rule. Proceed?');">
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
                        <i class="bi bi-robot display-4 text-muted opacity-25 d-block mb-3"></i>
                        <span class="text-muted small fw-bold uppercase tracking-widest">No AI intelligence rules active.</span>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<style>
    .extra-small { font-size: 0.65rem; }
    .fw-black { font-weight: 900; }
    .w-fit { width: fit-content; }
</style>
@endsection
