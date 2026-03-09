@extends('layouts.app')
@section('title', __('projects.title'))
@section('page-title', __('projects.title'))

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h2 class="h4 mb-1 fw-bold text-dark">{{ __('projects.title') }}</h2>
        <p class="text-muted small mb-0">{{ __('projects.subtitle', ['count' => $projects->total()]) }}</p>
    </div>
    @can('create project')
    <a href="{{ route('projects.create') }}" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm">
        <i class="bi bi-plus-lg"></i>
        {{ __('projects.create') }}
    </a>
    @endcan
</div>

{{-- Filters --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('projects.index') }}" class="row g-3">
            <div class="col-md-4">
                <label class="form-label small fw-bold text-muted text-uppercase">{{ __('common.search') }}</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('projects.search_placeholder') }}" class="form-control form-control-sm">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold text-muted text-uppercase">{{ __('projects.status') }}</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">{{ __('projects.all_statuses') }}</option>
                    @foreach(['planning', 'active', 'ongoing', 'on_hold', 'completed', 'cancelled'] as $st)
                    <option value="{{ $st }}" {{ request('status') === $st ? 'selected' : '' }}>{{ __('projects.statuses.' . $st) ?? ucfirst($st) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold text-muted text-uppercase">{{ __('projects.risk_level') }}</label>
                <select name="risk" class="form-select form-select-sm">
                    <option value="">{{ __('projects.all_levels') }}</option>
                    @foreach(['low', 'medium', 'high', 'critical'] as $rk)
                    <option value="{{ $rk }}" {{ request('risk') === $rk ? 'selected' : '' }}>{{ __('projects.risk_levels.' . $rk) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary btn-sm w-100">{{ __('common.search') }}</button>
            </div>
        </form>
    </div>
</div>

{{-- Project Grid --}}
<div class="row g-4">
    @forelse($projects as $project)
    <div class="col-xl-4 col-md-6">
        <div class="card h-100 border-0 shadow-sm project-card">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <code class="small text-primary fw-bold mb-1 d-block">{{ $project->code }}</code>
                        <h5 class="card-title fw-bold mb-0">
                            <a href="{{ route('projects.show', $project) }}" class="text-dark text-decoration-none hover-primary">{{ $project->name }}</a>
                        </h5>
                    </div>
                    <span class="badge {{ $project->status_badge_class }} rounded-pill px-3">{{ __('projects.statuses.'.$project->status) ?? ucfirst($project->status) }}</span>
                </div>

                <div class="row g-2 mb-4">
                    <div class="col-6">
                        <div class="p-2 rounded bg-light border text-center">
                            <small class="text-muted d-block text-uppercase extra-small fw-bold">Risk Score</small>
                            <span class="fw-bold fs-5 {{ $project->risk_score_color }}">{{ $project->risk_score }}</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2 rounded bg-light border text-center">
                            <small class="text-muted d-block text-uppercase extra-small fw-bold">Active Items</small>
                            <span class="fw-bold fs-5 text-primary">{{ $project->borrows()->where('status', 'borrowed')->count() }}</span>
                        </div>
                    </div>
                </div>

                <div class="vstack gap-2 small mb-4">
                    <div class="d-flex justify-content-between">
                        <span class="text-muted fw-semibold">Manager:</span>
                        <span class="text-dark fw-bold">{{ $project->manager?->name ?: 'Unassigned' }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted fw-semibold">PIC:</span>
                        <span class="text-dark fw-bold">{{ $project->pic?->name ?: 'Unassigned' }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted fw-semibold">Duration:</span>
                        <span class="text-dark fw-bold">{{ $project->start_date->format('d M') }} - {{ $project->end_date->format('d M Y') }}</span>
                    </div>
                </div>

                <div class="d-flex align-items-center justify-content-between mt-auto">
                    <div class="avatar-group d-flex">
                        @if($project->pic)
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($project->pic->name) }}&background=4e73df&color=fff" class="rounded-circle border border-2 border-white shadow-sm" style="width: 32px; height: 32px;" title="PIC: {{ $project->pic->name }}">
                        @endif
                        @if($project->manager)
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($project->manager->name) }}&background=1cc88a&color=fff" class="rounded-circle border border-2 border-white shadow-sm ms-n2" style="width: 32px; height: 32px; margin-left: -10px" title="Manager: {{ $project->manager->name }}">
                        @endif
                    </div>
                    <a href="{{ route('projects.show', $project) }}" class="btn btn-link btn-sm p-0 fw-bold text-decoration-none text-primary">
                        Details <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12 text-center py-5">
        <div class="bg-light d-inline-block p-4 rounded-circle mb-3">
            <i class="bi bi-folder2-open fs-1 text-muted"></i>
        </div>
        <h5 class="text-muted">No projects found.</h5>
        <p class="small text-muted mb-0">Try adjusting your filters or search query.</p>
    </div>
    @endforelse
</div>

@if($projects->hasPages())
<div class="mt-5 d-flex justify-content-between align-items-center pb-4">
    <span class="small text-muted fw-semibold">Showing items {{ $projects->firstItem() }} to {{ $projects->lastItem() }} of {{ $projects->total() }}</span>
    <div>
        {{ $projects->links('pagination::bootstrap-5') }}
    </div>
</div>
@endif

<style>
    .project-card:hover { 
        transform: translateY(-5px); 
        transition: all 0.3s ease;
        box-shadow: 0 0.5rem 2rem rgba(0,0,0,0.12) !important;
    }
    .extra-small { font-size: 0.65rem; }
    .hover-primary:hover { color: var(--bs-primary) !important; }
</style>
@endsection
