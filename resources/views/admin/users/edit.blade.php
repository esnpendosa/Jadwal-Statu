@extends('layouts.app')
@section('title', 'Edit User Account')
@section('page-title', 'User Management')

@section('content')
<div class="row justify-content-center fade-in">
    <div class="col-lg-10 col-xl-8">
        <div class="card border-0 shadow-lg overflow-hidden">
            {{-- Header --}}
            <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <div class="p-2 bg-primary bg-opacity-10 rounded-3 text-primary">
                        <i class="bi bi-person-gear fs-5"></i>
                    </div>
                    <h5 class="mb-0 fw-black text-dark text-uppercase tracking-tight">Edit User Account: {{ $user->name }}</h5>
                </div>
                <a href="{{ route('admin.users.index') }}" class="btn btn-light btn-sm fw-bold px-3 border shadow-sm">
                    <i class="bi bi-arrow-left me-1"></i> {{ __('common.back') }}
                </a>
            </div>
            
            <form action="{{ route('admin.users.update', $user) }}" method="POST" class="card-body p-4 p-md-5">
                @csrf
                @method('PUT')
                
                <div class="row g-4">
                    {{-- User Info Section --}}
                    <div class="col-md-7">
                        <div class="row g-4">
                            <div class="col-12">
                                <label class="form-label extra-small fw-black text-muted text-uppercase tracking-widest mb-1">Full Name <span class="text-danger">*</span></label>
                                <div class="input-group border rounded-3 overflow-hidden bg-white shadow-none trans-input">
                                    <span class="input-group-text bg-transparent border-0 pe-0"><i class="bi bi-person text-muted"></i></span>
                                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control border-0 shadow-none fw-bold small" required>
                                </div>
                                @error('name') <div class="extra-small text-danger mt-1 fw-bold">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label extra-small fw-black text-muted text-uppercase tracking-widest mb-1">Email Address <span class="text-danger">*</span></label>
                                <div class="input-group border rounded-3 overflow-hidden bg-white shadow-none trans-input">
                                    <span class="input-group-text bg-transparent border-0 pe-0"><i class="bi bi-envelope text-muted"></i></span>
                                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control border-0 shadow-none fw-bold small" required>
                                </div>
                                @error('email') <div class="extra-small text-danger mt-1 fw-bold">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label extra-small fw-black text-muted text-uppercase tracking-widest mb-1">Assigned Role <span class="text-danger">*</span></label>
                                <div class="input-group border rounded-3 overflow-hidden bg-white shadow-none trans-input">
                                    <span class="input-group-text bg-transparent border-0 pe-0"><i class="bi bi-shield-lock text-muted"></i></span>
                                    <select name="role" class="form-select border-0 shadow-none fw-bold small" required>
                                        @foreach($roles as $role)
                                        <option value="{{ $role->name }}" {{ (old('role', $user->getRoleNames()->first()) === $role->name) ? 'selected' : '' }}>
                                            {{ strtoupper($role->name) }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('role') <div class="extra-small text-danger mt-1 fw-bold">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12">
                                <div class="bg-light bg-opacity-50 p-3 rounded-4 border border-dashed text-dark">
                                    <div class="form-check form-switch m-0">
                                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }} class="form-check-input" id="isActiveSwitch">
                                        <label class="form-check-label small fw-black text-uppercase tracking-widest cursor-pointer" for="isActiveSwitch">
                                            Account Status: {{ $user->is_active ? 'ACTIVE' : 'INACTIVE' }}
                                        </label>
                                    </div>
                                    <small class="extra-small text-muted d-block mt-1">If inactive, the user will be blocked from logging into the platform.</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Security Column --}}
                    <div class="col-md-5">
                        <div class="p-4 rounded-4 border border-light h-100" style="background-color: #f8faff;">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <div class="p-2 bg-dark rounded-3 text-white" style="width: 32px; height: 32px; display: flex; align-items:center; justify-content:center;">
                                    <i class="bi bi-key-fill extra-small"></i>
                                </div>
                                <h6 class="extra-small fw-black text-dark text-uppercase tracking-widest mb-0">Security Credentials</h6>
                            </div>
                            
                            <p class="extra-small text-muted mb-4 italic">
                                Leave the password fields empty if you do not want to change the user's current password.
                            </p>

                            <div class="mb-3">
                                <label class="form-label extra-small fw-bold text-dark text-uppercase">New Password</label>
                                <input type="password" name="password" class="form-control form-control-sm border shadow-none" placeholder="Keep empty to skip">
                                @error('password') <div class="extra-small text-danger mt-1 fw-bold">{{ $message }}</div> @enderror
                            </div>
                            
                            <div class="mb-0">
                                <label class="form-label extra-small fw-bold text-dark text-uppercase">Confirm Password</label>
                                <input type="password" name="password_confirmation" class="form-control form-control-sm border shadow-none">
                            </div>

                            <div class="mt-4 pt-3 border-top border-light">
                                <div class="extra-small text-muted d-flex align-items-start gap-2">
                                    <i class="bi bi-info-circle-fill text-primary"></i>
                                    <span>Passwords must be at least 8 characters long and match the confirmation field.</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="mt-5 pt-4 border-top d-flex align-items-center justify-content-end gap-3">
                    <button type="reset" class="btn btn-light px-4 fw-bold border shadow-none text-muted">BATAL</button>
                    <button type="submit" class="btn btn-primary px-5 fw-black shadow-sm text-uppercase">
                        <i class="bi bi-cloud-arrow-up-fill me-1"></i> Update User Account
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .fw-black { font-weight: 900; }
    .extra-small { font-size: 0.65rem; }
    .fade-in { animation: fadeIn 0.5s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    .trans-input { transition: all 0.2s ease; }
    .trans-input:focus-within { border-color: #4e73df !important; box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.1) !important; }
    .bg-opacity-05 { background-color: rgba(0, 0, 0, 0.02); }
</style>
@endsection
