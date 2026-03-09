@extends('layouts.app')
@section('title', __('projects.edit') . ': ' . $project->name)
@section('page-title', __('projects.title'))

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('projects.show', $project) }}" class="btn btn-light border btn-sm shadow-sm">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h2 class="h4 mb-0 fw-bold text-dark text-uppercase tracking-tight">{{ __('projects.edit') }}</h2>
                    <div class="d-flex align-items-center gap-2 mt-1">
                        <code class="extra-small text-muted bg-light px-2 rounded">{{ $project->code }}</code>
                        <span class="badge {{ $project->status_badge_class }} rounded-pill px-3 py-1 extra-small">
                            {{ strtoupper(__('projects.statuses.' . $project->status)) }}
                        </span>
                    </div>
                </div>
            </div>
            <div>
                <span class="badge {{ $project->risk_badge_class }} rounded-pill px-3 py-2 extra-small border">
                    <i class="bi bi-shield-exclamation me-1"></i> RISK: {{ strtoupper(__('projects.risk_levels.' . ($project->risk_level ?? 'low'))) }}
                </span>
            </div>
        </div>

        <form action="{{ route('projects.update', $project) }}" method="POST" id="project-form">
            @csrf
            @method('PUT')

            @if($errors->any())
            <div class="alert alert-danger border-0 shadow-sm mb-4">
                <div class="d-flex align-items-center mb-2">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <strong class="small text-uppercase">{{ __('common.fix_errors') }}</strong>
                </div>
                <ul class="mb-0 extra-small">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="row g-4">
                {{-- Main Info --}}
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="mb-0 fw-bold text-dark text-uppercase small tracking-wider">{{ __('projects.project_info') }}</h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted text-uppercase">{{ __('projects.name') }} <span class="text-danger">*</span></label>
                                <input type="text" name="name" value="{{ old('name', $project->name) }}" class="form-control @error('name') is-invalid @enderror" required>
                                @error('name') <div class="invalid-feedback text-danger extra-small">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted text-uppercase">{{ __('projects.location') }} <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-geo-alt"></i></span>
                                    <input type="text" name="location" value="{{ old('location', $project->location) }}" class="form-control border-start-0 ps-0 @error('location') is-invalid @enderror" required>
                                </div>
                                @error('location') <div class="text-danger extra-small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted text-uppercase">{{ __('projects.client') }}</label>
                                <input type="text" name="client_name" value="{{ old('client_name', $project->client_name) }}" class="form-control">
                            </div>

                            <div class="mb-0">
                                <label class="form-label small fw-bold text-muted text-uppercase">{{ __('common.notes') }}</label>
                                <textarea name="description" rows="4" class="form-control">{{ old('description', $project->description) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="mb-0 fw-bold text-dark text-uppercase small tracking-wider">{{ __('projects.timeline') }}</h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted text-uppercase">{{ __('projects.start_date') }} <span class="text-danger">*</span></label>
                                    <input type="date" name="start_date" value="{{ old('start_date', $project->start_date->format('Y-m-d')) }}" class="form-control @error('start_date') is-invalid @enderror" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted text-uppercase">{{ __('projects.end_date') }} <span class="text-danger">*</span></label>
                                    <input type="date" name="end_date" value="{{ old('end_date', $project->end_date->format('Y-m-d')) }}" class="form-control @error('end_date') is-invalid @enderror" required>
                                </div>
                            </div>
                            <div class="mt-4 p-3 rounded bg-info bg-opacity-10 border border-info border-opacity-25" id="duration-alert">
                                <div class="d-flex align-items-center text-info">
                                    <i class="bi bi-calendar-range me-2"></i>
                                    <span class="small fw-bold" id="duration-display">{{ __('projects.duration_title') }}: {{ $project->duration_days }} {{ __('projects.days') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Sidebar --}}
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="mb-0 fw-bold text-dark text-uppercase small tracking-wider">Project Lifecycle</h6>
                        </div>
                        <div class="card-body p-4">
                            <label class="form-label small fw-bold text-muted text-uppercase">{{ __('projects.status') }} <span class="text-danger">*</span></label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                @foreach(['planning' => __('projects.statuses.planning'), 'active' => __('projects.statuses.active'), 'on_hold' => __('projects.statuses.on_hold'), 'draft' => __('projects.statuses.draft'), 'completed' => __('projects.statuses.completed'), 'cancelled' => __('projects.statuses.cancelled')] as $val => $label)
                                <option value="{{ $val }}" {{ old('status', $project->status) === $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="mb-0 fw-bold text-dark text-uppercase small tracking-wider">{{ __('projects.personnel') }}</h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted text-uppercase">{{ __('projects.manager') }}</label>
                                <input type="text" name="manager_name"
                                    value="{{ old('manager_name', $project->manager_name) }}"
                                    placeholder="Nama Manajer Proyek..."
                                    class="form-control @error('manager_name') is-invalid @enderror">
                                <p class="text-muted extra-small mt-1 mb-0">Ketik nama manajer secara manual</p>
                                @error('manager_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="mb-0">
                                <label class="form-label small fw-bold text-muted text-uppercase">{{ __('projects.pic') }}</label>
                                <select name="pic_id" class="form-select">
                                    <option value="">— Select PIC —</option>
                                    @foreach($pics as $pic)
                                    <option value="{{ $pic->id }}" {{ old('pic_id', $project->pic_id) == $pic->id ? 'selected' : '' }}>{{ $pic->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    @if($project->risk_score)
                    <div class="card border-0 shadow-sm bg-dark text-white mb-4 overflow-hidden">
                        <div class="card-body p-4 text-center position-relative">
                            <i class="bi bi-shield-slash position-absolute text-white opacity-10" style="font-size: 5rem; top: -10px; right: -10px;"></i>
                            <small class="text-uppercase extra-small fw-bold tracking-widest opacity-50">{{ __('projects.risk_score') }}</small>
                            <h1 class="display-4 fw-black my-2 {{ $project->risk_score_color }}">{{ $project->risk_score }}</h1>
                            <span class="badge {{ $project->risk_badge_class }} rounded-pill px-3 py-1 extra-small">
                                {{ strtoupper(__('projects.risk_levels.' . ($project->risk_level ?? 'low'))) }}
                            </span>
                        </div>
                    </div>
                    @endif

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary py-3 fw-bold shadow-sm">
                            <i class="bi bi-save me-2"></i> {{ __('common.save') }}
                        </button>
                        <a href="{{ route('projects.show', $project) }}" class="btn btn-light border py-3 fw-bold text-muted">
                            {{ __('common.cancel') }}
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
    .extra-small { font-size: 0.65rem; }
    .fw-black { font-weight: 900; }
</style>
@endsection

@push('scripts')
<script>
function updateDuration() {
    const startInput = document.querySelector('input[name="start_date"]');
    const endInput   = document.querySelector('input[name="end_date"]');
    const display    = document.getElementById('duration-display');
    if (!startInput || !endInput || !display) return;
    const start = new Date(startInput.value);
    const end   = new Date(endInput.value);
    if (!isNaN(start) && !isNaN(end) && end >= start) {
        const days = Math.round((end - start) / (1000 * 60 * 60 * 24));
        display.innerHTML = `Project edited duration: <strong>${days} days</strong>`;
    }
}
document.querySelector('input[name="start_date"]')?.addEventListener('change', updateDuration);
document.querySelector('input[name="end_date"]')?.addEventListener('change', updateDuration);
</script>
@endpush
