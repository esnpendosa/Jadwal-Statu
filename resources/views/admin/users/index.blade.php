@extends('layouts.app')
@section('title', 'User Management')
@section('page-title', 'User Management')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 fade-in">
    <div>
        <h2 class="h4 mb-1 fw-black text-dark text-uppercase tracking-tight">User Management</h2>
        <p class="text-muted extra-small mb-0 fw-bold text-uppercase tracking-widest">Manage system administrators, warehouse staff, and field PICs.</p>
    </div>
    @can('manage users')
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary d-flex align-items-center gap-2 px-3 fw-bold shadow-sm">
        <i class="bi bi-person-plus-fill"></i>
        <span>ADD NEW USER</span>
    </a>
    @endcan
</div>

{{-- User List --}}
<div class="card border-0 shadow-sm overflow-hidden fade-in">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr class="extra-small text-muted text-uppercase fw-black tracking-widest">
                    <th class="ps-4 py-3">Full Name & Email</th>
                    <th class="py-3">Role</th>
                    <th class="py-3">Risk Profile</th>
                    <th class="text-center py-3">Status</th>
                    <th class="text-end pe-4 py-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr class="small transition-all">
                    <td class="ps-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="position-relative">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=4e73df&color=fff&size=48&bold=true" class="rounded-circle shadow-sm border border-2 border-white" alt="">
                                @if($user->is_active)
                                <span class="position-absolute bottom-0 end-0 bg-success border border-2 border-white rounded-circle" style="width: 12px; height: 12px;"></span>
                                @endif
                            </div>
                            <div>
                                <div class="fw-bold text-dark mb-0">{{ $user->name }}</div>
                                <code class="extra-small text-muted font-monospace opacity-75">{{ $user->email }}</code>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex flex-wrap gap-1">
                            @forelse($user->getRoleNames() as $role)
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 extra-small fw-black rounded-pill">{{ strtoupper($role) }}</span>
                            @empty
                            <span class="text-muted extra-small italic">NO ROLE</span>
                            @endforelse
                        </div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <span class="extra-small fw-black {{ $user->risk_score > 50 ? 'text-danger' : ($user->risk_score > 20 ? 'text-warning' : 'text-success') }}">
                                {{ $user->risk_score }}
                            </span>
                            <div class="progress rounded-pill overflow-hidden" style="width: 80px; height: 6px; background-color: #f0f2f5;">
                                <div class="progress-bar {{ $user->risk_score > 50 ? 'bg-danger' : ($user->risk_score > 20 ? 'bg-warning' : 'bg-success') }}" 
                                     role="progressbar" style="width: {{ min($user->risk_score, 100) }}%"></div>
                            </div>
                        </div>
                    </td>
                    <td class="text-center">
                        <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-sm py-1 px-3 rounded-pill border-0 {{ $user->is_active ? 'btn-success-subtle text-success' : 'btn-danger-subtle text-danger' }} extra-small fw-black text-uppercase shadow-none transition-all">
                                {{ $user->is_active ? 'ACTIVE' : 'INACTIVE' }}
                            </button>
                        </form>
                    </td>
                    <td class="text-end pe-4">
                        <div class="d-flex justify-content-end gap-1">
                            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-light border-0 text-primary shadow-none hover-lift" title="Edit User">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            @if($user->id !== auth()->id())
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Delete this user?');" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-light border-0 text-danger shadow-none hover-lift" title="Delete User">
                                    <i class="bi bi-trash3-fill"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-5 text-center">
                        <i class="bi bi-people display-4 text-muted opacity-25 d-block mb-3"></i>
                        <span class="text-muted extra-small fw-black text-uppercase tracking-widest">No users found in the directory.</span>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($users->hasPages())
    <div class="card-footer bg-white border-0 px-4 py-3">
        <div class="pagination-container">
            <p class="extra-small text-muted mb-0 fw-bold text-uppercase tracking-widest">
                TOTAL: {{ $users->total() }} SYSTEM USERS
            </p>
            <div>
                {{ $users->links() }}
            </div>
        </div>
    </div>
    @endif
</div>

<style>
    .fw-black { font-weight: 900; }
    .extra-small { font-size: 0.65rem; }
    .btn-success-subtle { background-color: rgba(25, 135, 84, 0.1); }
    .btn-danger-subtle { background-color: rgba(220, 53, 69, 0.1); }
    .btn-success-subtle:hover { background-color: rgba(25, 135, 84, 0.2); }
    .btn-danger-subtle:hover { background-color: rgba(220, 53, 69, 0.2); }
    .fade-in { animation: fadeIn 0.5s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    .transition-all { transition: all 0.2s ease; }
    .hover-lift:hover { transform: translateY(-2px); }
</style>
@endsection
