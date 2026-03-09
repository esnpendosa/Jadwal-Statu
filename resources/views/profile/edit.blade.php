@extends('layouts.app')
@section('title', __('profile.title'))
@section('page-title', __('profile.title'))

@section('content')
<div class="row g-4 fade-in">
    {{-- Profile Sidebar --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-lg overflow-hidden h-100">
            <!-- Header Background with Gradient -->
            <div class="position-relative" style="height: 120px; background: linear-gradient(45deg, #4e73df, #224abe);">
                <div class="position-absolute start-50 top-100 translate-middle">
                    <div class="avatar-container">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=fff&color=4e73df&size=128&bold=true" 
                             class="rounded-circle border border-4 border-white shadow-lg" style="width: 100px; height: 100px; object-fit: cover;">
                        <span class="status-indicator bg-success"></span>
                    </div>
                </div>
            </div>
            
            <div class="card-body pt-5 text-center mt-3">
                <h4 class="fw-black text-dark mb-1">{{ auth()->user()->name }}</h4>
                <p class="text-muted small mb-3 text-uppercase tracking-widest fw-bold">
                    <i class="bi bi-shield-fill-check text-primary me-1"></i>
                    {{ auth()->user()->roles->first()->name ?? 'System User' }}
                </p>
                
                <div class="d-flex justify-content-center gap-2 mb-4">
                    <div class="p-2 border rounded-3 flex-grow-1 bg-light bg-opacity-50">
                        <small class="d-block text-muted extra-small fw-black">SINCE</small>
                        <span class="small fw-bold">{{ auth()->user()->created_at->format('M Y') }}</span>
                    </div>
                </div>

                <hr class="opacity-10">

                {{-- Language Settings --}}
                <div class="text-start px-2">
                    <h6 class="extra-small fw-black text-muted text-uppercase tracking-widest mb-3">UI PREFERENCE</h6>
                    <form action="{{ route('profile.language') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="d-flex flex-column gap-2">
                            @foreach(['id' => 'Indonesia', 'en' => 'English (US)', 'zh' => 'Mandarin'] as $code => $label)
                            <label class="lang-card {{ auth()->user()->preferred_language === $code ? 'active' : '' }}">
                                <input type="radio" name="locale" value="{{ $code }}" class="d-none" onchange="this.form.submit()" {{ auth()->user()->preferred_language === $code ? 'checked' : '' }}>
                                <div class="d-flex align-items-center justify-content-between w-100">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="lang-icon">
                                            @if($code == 'id') 🇮🇩 @elseif($code == 'en') 🇺🇸 @else 🇨🇳 @endif
                                        </div>
                                        <span class="small fw-bold">{{ $label }}</span>
                                    </div>
                                    <i class="bi bi-chevron-right extra-small text-muted"></i>
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Column: Forms --}}
    <div class="col-lg-8">
        {{-- General Info Card --}}
        <div class="card border-0 shadow-lg mb-4">
            <div class="card-header bg-white py-3 border-bottom border-light">
                <div class="d-flex align-items-center gap-2">
                    <div class="p-2 bg-primary bg-opacity-10 rounded-3 text-primary">
                        <i class="bi bi-person-workspace fs-5"></i>
                    </div>
                    <h5 class="m-0 fw-black text-dark fs-6">{{ __('profile.personal_info') }}</h5>
                </div>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label extra-small fw-black text-muted text-uppercase tracking-widest">{{ __('profile.name') }}</label>
                            <div class="input-group input-group-lg border rounded-3 overflow-hidden bg-white">
                                <span class="input-group-text bg-transparent border-0 pe-0"><i class="bi bi-person text-muted"></i></span>
                                <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" class="form-control border-0 shadow-none fw-bold small" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label extra-small fw-black text-muted text-uppercase tracking-widest">{{ __('profile.email_readonly') }}</label>
                            <div class="input-group input-group-lg border rounded-3 overflow-hidden bg-light bg-opacity-75">
                                <span class="input-group-text bg-transparent border-0 pe-0"><i class="bi bi-envelope text-muted"></i></span>
                                <input type="email" value="{{ auth()->user()->email }}" class="form-control border-0 shadow-none fw-bold small text-muted" disabled>
                            </div>
                            <small class="extra-small text-muted mt-2 d-block italic"><i class="bi bi-info-circle me-1"></i> {{ __('profile.email_help') }}</small>
                        </div>
                        <div class="col-12 text-end">
                            <button type="submit" class="btn btn-primary px-5 fw-black text-uppercase shadow-sm">
                                <i class="bi bi-check2-circle me-1"></i> {{ __('common.save') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Security Card --}}
        <div class="card border-0 shadow-lg border-start border-warning border-4">
            <div class="card-header bg-white py-3 border-bottom border-light">
                <div class="d-flex align-items-center gap-2">
                    <div class="p-2 bg-warning bg-opacity-10 rounded-3 text-warning">
                        <i class="bi bi-shield-lock-fill fs-5"></i>
                    </div>
                    <h5 class="m-0 fw-black text-dark fs-6">SECURITY & AUTHENTICATION</h5>
                </div>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('profile.update_password') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-4">
                        <label class="form-label extra-small fw-black text-muted text-uppercase tracking-widest">{{ __('profile.current_password') }}</label>
                        <div class="input-group border rounded-3 overflow-hidden">
                            <span class="input-group-text bg-transparent border-0 pe-0"><i class="bi bi-key-fill text-muted"></i></span>
                            <input type="password" name="current_password" class="form-control border-0 shadow-none fw-bold small" placeholder="Enter your current password" required>
                        </div>
                    </div>

                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label class="form-label extra-small fw-black text-muted text-uppercase tracking-widest">{{ __('profile.new_password') }}</label>
                            <div class="input-group border rounded-3 overflow-hidden">
                                <span class="input-group-text bg-transparent border-0 pe-0"><i class="bi bi-lock-fill text-muted"></i></span>
                                <input type="password" name="password" class="form-control border-0 shadow-none fw-bold small" placeholder="Minimum 8 characters" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label extra-small fw-black text-muted text-uppercase tracking-widest">{{ __('profile.confirm_password') }}</label>
                            <div class="input-group border rounded-3 overflow-hidden">
                                <span class="input-group-text bg-transparent border-0 pe-0"><i class="bi bi-lock-check-fill text-muted"></i></span>
                                <input type="password" name="password_confirmation" class="form-control border-0 shadow-none fw-bold small" placeholder="Repeat new password" required>
                            </div>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-dark px-5 fw-black text-uppercase shadow-sm">
                            <i class="bi bi-shield-check me-1"></i> {{ __('profile.update_password') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .fw-black { font-weight: 900; }
    .extra-small { font-size: 0.65rem; }
    .status-indicator {
        position: absolute;
        bottom: 8px;
        right: 8px;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        border: 3px solid #fff;
    }
    .lang-card {
        cursor: pointer;
        padding: 12px 16px;
        border-radius: 12px;
        border: 1px solid #edf2f7;
        background: #fff;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        display: block;
        margin: 0;
    }
    .lang-card:hover { border-color: #4e73df; background: #f8faff; transform: translateX(5px); }
    .lang-card.active { border-color: #4e73df; background: #ebf1ff; box-shadow: 0 4px 6px rgba(78, 115, 223, 0.1); }
    .lang-icon { font-size: 1.1rem; filter: grayscale(0.5); transition: 0.2s; }
    .lang-card.active .lang-icon { filter: grayscale(0); }
    .fade-in { animation: fadeIn 0.5s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    .hover-lift:hover { transform: translateY(-5px); }
    .transition-all { transition: all 0.3s ease; }
</style>
@endsection
