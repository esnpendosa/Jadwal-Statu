@extends('layouts.app')
@section('title', __('projects.create'))
@section('page-title', __('projects.title'))

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="d-flex align-items-center gap-3 mb-4">
            <a href="{{ route('projects.index') }}" class="btn btn-light border btn-sm shadow-sm">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h2 class="h4 mb-0 fw-bold text-dark text-uppercase tracking-tight">{{ __('projects.create') }}</h2>
                <p class="text-muted small mb-0">{{ __('projects.create_subtitle') }}</p>
            </div>
        </div>

        <form action="{{ route('projects.store') }}" method="POST" id="project-form">
            @csrf

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
                                <input type="text" name="name" value="{{ old('name') }}" placeholder="{{ __('projects.name_placeholder') }}" class="form-control @error('name') is-invalid @enderror" required>
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted text-uppercase">{{ __('projects.location') }} <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-geo-alt"></i></span>
                                    <input type="text" name="location" value="{{ old('location') }}" placeholder="{{ __('projects.location_placeholder') }}" class="form-control border-start-0 ps-0 @error('location') is-invalid @enderror" required>
                                </div>
                                @error('location') <div class="text-danger extra-small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted text-uppercase">{{ __('projects.client') }}</label>
                                <input type="text" name="client_name" value="{{ old('client_name') }}" placeholder="{{ __('projects.client_placeholder') }}" class="form-control">
                            </div>

                            <div class="mb-0">
                                <label class="form-label small fw-bold text-muted text-uppercase">{{ __('common.notes') }}</label>
                                <textarea name="description" rows="4" class="form-control" placeholder="{{ __('projects.description_placeholder') }}">{{ old('description') }}</textarea>
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
                                    <input type="date" name="start_date" value="{{ old('start_date', date('Y-m-d')) }}" class="form-control @error('start_date') is-invalid @enderror" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted text-uppercase">{{ __('projects.end_date') }} <span class="text-danger">*</span></label>
                                    <input type="date" name="end_date" value="{{ old('end_date', date('Y-m-d', strtotime('+30 days'))) }}" class="form-control @error('end_date') is-invalid @enderror" required>
                                </div>
                            </div>
                            <div class="mt-4 p-3 rounded bg-info bg-opacity-10 border border-info border-opacity-25" id="duration-alert">
                                <div class="d-flex align-items-center text-info">
                                    <i class="bi bi-calendar-range me-2"></i>
                                    <span class="small fw-bold" id="duration-display">Estimated duration: 30 days</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Sidebar --}}
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="mb-0 fw-bold text-dark text-uppercase small tracking-wider">Project Status</h6>
                        </div>
                        <div class="card-body p-4">
                            <label class="form-label small fw-bold text-muted text-uppercase">{{ __('projects.status') }} <span class="text-danger">*</span></label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                @foreach(['planning' => __('projects.statuses.planning'), 'active' => __('projects.statuses.active'), 'on_hold' => __('projects.statuses.on_hold'), 'draft' => __('projects.statuses.draft')] as $val => $label)
                                <option value="{{ $val }}" {{ old('status', 'planning') === $val ? 'selected' : '' }}>{{ $label }}</option>
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
                                    value="{{ old('manager_name') }}"
                                    placeholder="Nama Manajer Proyek..."
                                    class="form-control @error('manager_name') is-invalid @enderror">
                                <p class="text-muted extra-small mt-1 mb-0">Ketik nama manajer secara manual</p>
                                @error('manager_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="mb-0">
                                <label class="form-label small fw-bold text-muted text-uppercase">{{ __('projects.pic') }}</label>
                                <select name="pic_id" class="form-select @error('pic_id') is-invalid @enderror">
                                    <option value="">— Select PIC —</option>
                                    @foreach($pics as $pic)
                                    <option value="{{ $pic->id }}" {{ old('pic_id') == $pic->id ? 'selected' : '' }}>{{ $pic->name }}</option>
                                    @endforeach
                                </select>
                                <p class="text-muted extra-small mt-2 mb-0">{{ __('projects.pic_hint') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary py-3 fw-bold shadow-sm">
                            <i class="bi bi-check-circle me-2"></i> {{ __('projects.create') }}
                        </button>
                        <a href="{{ route('projects.index') }}" class="btn btn-light border py-3 fw-bold text-muted">
                            {{ __('common.cancel') }}
                        </a>
                    </div>

                    <div class="mt-4 p-4 rounded bg-warning bg-opacity-10 border border-warning border-opacity-25">
                        <div class="d-flex align-items-start gap-2 text-warning">
                            <i class="bi bi-shield-lock-fill"></i>
                            <div>
                                <h6 class="extra-small fw-black text-uppercase mb-1 tracking-wider">{{ __('projects.risk_info_title') }}</h6>
                                <p class="extra-small mb-0 opacity-75">{{ __('projects.risk_info_body') }}</p>
                            </div>
                        </div>
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
        display.innerHTML = `This project will span approximately <strong>${days} days</strong>`;
    }
}
document.querySelector('input[name="start_date"]')?.addEventListener('change', updateDuration);
document.querySelector('input[name="end_date"]')?.addEventListener('change', updateDuration);
updateDuration();
</script>
@endpush
