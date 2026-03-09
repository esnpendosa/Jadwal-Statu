@extends('layouts.app')
@section('title', 'System Settings')
@section('page-title', 'System Settings')

@section('content')
<div class="row g-4">
    <div class="col-lg-4">
        {{-- Navigation Menu --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-black text-primary text-uppercase small tracking-wider">Configuration Menu</h6>
            </div>
            <div class="p-2">
                <div class="nav flex-column nav-pills" id="settings-tabs" role="tablist" aria-orientation="vertical">
                    <button class="nav-link active text-start py-3 px-4 mb-1 border-0 fw-bold small text-uppercase" id="general-tab" data-bs-toggle="pill" data-bs-target="#tab-general" type="button" role="tab">
                        <i class="bi bi-gear-fill me-2"></i> System Defaults
                    </button>
                    <button class="nav-link text-start py-3 px-4 mb-1 border-0 fw-bold small text-uppercase" id="google-tab" data-bs-toggle="pill" data-bs-target="#tab-google" type="button" role="tab">
                        <i class="bi bi-google me-2"></i> Google Integration
                    </button>
                </div>
            </div>
        </div>

        {{-- Google Status Card --}}
        @php 
            $isLinked = \App\Models\SystemSetting::get('google_calendar_token') ? true : false;
        @endphp
        <div class="card border-0 shadow-sm {{ $isLinked ? 'bg-success bg-opacity-10' : 'bg-light' }}">
            <div class="card-body p-4 text-center">
                <div class="mb-3">
                    <div class="d-inline-flex p-3 rounded-circle {{ $isLinked ? 'bg-success text-white' : 'bg-secondary text-white' }}">
                        <i class="bi bi-calendar-check fs-4"></i>
                    </div>
                </div>
                <h6 class="fw-bold text-dark mb-1">Google Workspace</h6>
                <p class="small text-muted mb-3">Calendar Synchronization</p>
                <span class="badge {{ $isLinked ? 'bg-success' : 'bg-secondary' }} rounded-pill px-3 py-2">
                    {{ $isLinked ? 'LINKED' : 'UNLINKED' }}
                </span>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="tab-content" id="settings-tabsContent">
            {{-- General Tab --}}
            <div class="tab-pane fade show active" id="tab-general" role="tabpanel">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 fw-bold text-dark uppercase small">Operational Defaults</h6>
                    </div>
                    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="card-body p-4">
                        @csrf
                        <div class="row g-4">
                            {{-- Branding --}}
                            <div class="col-12 border-bottom pb-4">
                                <h6 class="text-primary small fw-bold text-uppercase mb-4 tracking-widest">Branding & Identity</h6>
                                
                                <div class="row align-items-center g-3">
                                    <div class="col-auto">
                                        <div class="bg-light rounded-3 p-2 border" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center;">
                                            @if($logo = \App\Models\SystemSetting::get('app_logo'))
                                                <img src="{{ asset('storage/' . $logo) }}" class="img-fluid" style="max-height: 100%;">
                                            @else
                                                <i class="bi bi-image fs-1 text-muted opacity-25"></i>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col">
                                        <label class="form-label small fw-bold text-muted text-uppercase mb-1">Application Logo</label>
                                        <input type="file" name="app_logo" accept="image/*" class="form-control form-control-sm">
                                        <p class="text-muted mt-2 mb-0" style="font-size: 0.65rem">PNG with transparent background recommended. Max 2MB.</p>
                                    </div>
                                </div>
                                
                                <div class="mt-4">
                                    <label class="form-label small fw-bold text-muted text-uppercase">Application / Company Name</label>
                                    <input type="text" name="settings[app_name]" value="{{ \App\Models\SystemSetting::get('app_name', 'Smart Inventory') }}" class="form-control" placeholder="e.g. PT. Global Solusi Utama">
                                </div>
                            </div>

                            {{-- Forms --}}
                            <div class="col-md-6 mt-4">
                                <label class="form-label small fw-bold text-muted text-uppercase">Company Address</label>
                                <textarea name="settings[company_address]" rows="3" class="form-control small" placeholder="Full office address...">{{ \App\Models\SystemSetting::get('company_address') }}</textarea>
                            </div>
                            <div class="col-md-6 mt-4">
                                <label class="form-label small fw-bold text-muted text-uppercase">Phone / WhatsApp</label>
                                <input type="text" name="settings[company_phone]" value="{{ \App\Models\SystemSetting::get('company_phone') }}" class="form-control" placeholder="+62 812...">
                                <div class="mt-3">
                                    <label class="form-label small fw-bold text-muted text-uppercase">Support Email</label>
                                    <input type="email" name="settings[company_email]" value="{{ \App\Models\SystemSetting::get('company_email') }}" class="form-control">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase">Default System Locale</label>
                                <select name="settings[default_locale]" class="form-select">
                                    <option value="id" {{ \App\Models\SystemSetting::get('default_locale') === 'id' ? 'selected' : '' }}>Bahasa Indonesia</option>
                                    <option value="en" {{ \App\Models\SystemSetting::get('default_locale') === 'en' ? 'selected' : '' }}>English (US)</option>
                                    <option value="zh" {{ \App\Models\SystemSetting::get('default_locale') === 'zh' ? 'selected' : '' }}>Mandarin Chinese (中文)</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase">Borrow Limit (Days)</label>
                                <div class="input-group">
                                    <input type="number" name="settings[default_borrow_limit]" value="{{ \App\Models\SystemSetting::get('default_borrow_limit', 7) }}" class="form-control">
                                    <span class="input-group-text bg-light small fw-bold">DAYS</span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-5 pt-4 border-top d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary px-5 shadow-sm fw-bold">SAVE SYSTEM SETTINGS</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Google Tab --}}
            <div class="tab-pane fade" id="tab-google" role="tabpanel">
                <div class="card border-0 shadow-sm overflow-hidden">
                    <div class="card-header bg-dark py-4 px-4 d-flex align-items-center justify-content-between border-0">
                        <div>
                            <h5 class="mb-1 text-white fw-bold">Google Calendar API</h5>
                            <p class="text-light opacity-50 small mb-0 font-monospace" style="font-size: 0.6rem; letter-spacing: 2px">PROJECT SCHEDULE SYNC</p>
                        </div>
                        <i class="bi bi-google fs-1 text-white opacity-25"></i>
                    </div>
                    <div class="card-body p-5 text-center">
                        <div class="alert alert-info border-0 shadow-sm bg-info bg-opacity-10 text-start mb-4">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-info-circle-fill fs-4 me-3 text-info"></i>
                                <span class="small text-dark">Ensure your <strong>GOOGLE_CLIENT_ID</strong> and <strong>SECRET</strong> are configured in the <code>.env</code> file before linking.</span>
                            </div>
                        </div>

                        <p class="text-muted mb-5 px-lg-5">
                            Linking a Google account allows the system to automatically post return deadlines and project schedules to a shared corporate calendar, visible to all stakeholders.
                        </p>

                        <div class="d-grid col-lg-8 mx-auto">
                            @if(!$isLinked)
                            <a href="{{ route('admin.google.auth') }}" class="btn btn-primary btn-lg py-3 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-3 border-0" style="background: #4285F4;">
                                <div class="bg-white rounded-circle p-1 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                    <svg width="20" height="20" viewBox="0 0 24 24"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
                                </div>
                                <span class="text-white">SIGN IN WITH GOOGLE WORKSPACE</span>
                            </a>
                            @else
                            <div class="p-4 rounded-3 border bg-light d-flex flex-column align-items-center">
                                <div class="badge bg-success py-2 px-4 rounded-pill mb-3">AUTHORIZED ✅</div>
                                <form action="{{ route('admin.google.revoke') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-link btn-sm text-danger text-decoration-none fw-bold uppercase tracking-widest" style="font-size: 0.65rem">REVOKE ALL ACCESS</button>
                                </form>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    #settings-tabs .nav-link { 
        color: #4e73df; 
        background: #2968ddc9; 
        transition: all 0.25s ease;
        border: 1px solid rgba(52, 100, 245, 0.77) !important;
        margin-bottom: 8px;
        font-weight: 700;
        opacity: 1;
    }
    #settings-tabs .nav-link:hover { 
        background: rgba(56, 109, 255, 0.68); 
        color: #5374d8ff; 
    }
    #settings-tabs .nav-link.active { 
        background: #4e73df !important; 
        color: #ffffff !important; 
        border-color: #4e73df !important;
        box-shadow: 0 4px 12px rgba(78, 115, 223, 0.25); 
    }
    #settings-tabs .nav-link.active i {
        color: #ffffff !important;
    }
    #settings-tabs .nav-link:not(.active) i {
        color: #4e73df;
    }
</style>
@endsection
