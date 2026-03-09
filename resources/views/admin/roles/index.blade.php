@extends('layouts.app')
@section('title', 'Roles & Permissions')
@section('page-title', 'Admin Panel')

@section('content')
<div class="space-y-6">
    <div class="page-header">
        <div>
            <h2 class="page-title text-gray-900 dark:text-white uppercase tracking-tight">Roles & Permissions</h2>
            <p class="page-subtitle">Configure security roles and granular access control</p>
        </div>
        @can('manage roles')
        <a href="{{ route('admin.roles.create') }}" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add New Role
        </a>
        @endcan
    </div>

    {{-- Role Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($roles as $role)
        <div class="card flex flex-col hover:shadow-card-hover transition-all duration-300">
            <div class="card-header border-none py-6 flex flex-col items-center">
                <div class="w-16 h-16 rounded-2xl bg-primary-50 dark:bg-primary-900/20 flex items-center justify-center text-primary-600 mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <h3 class="text-xl font-extrabold text-gray-900 dark:text-white uppercase tracking-tight">{{ $role->name }}</h3>
                <span class="text-[10px] font-bold text-gray-400 mt-1 uppercase tracking-widest">System Security Role</span>
            </div>
            <div class="card-body p-0 flex-1">
                <div class="px-6 py-4 flex items-center justify-between border-y border-gray-50 dark:border-gray-800">
                    <div class="text-center">
                        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Permissions</p>
                        <p class="text-lg font-black text-gray-900 dark:text-white">{{ $role->permissions->count() }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Active Users</p>
                        <p class="text-lg font-black text-gray-900 dark:text-white">{{ \App\Models\User::role($role->name)->count() }}</p>
                    </div>
                </div>
                
                <div class="p-6">
                    <h4 class="text-[10px] font-bold uppercase text-gray-400 tracking-widest mb-3">Key Permissions</h4>
                    <div class="flex flex-wrap gap-1.5 h-20 overflow-hidden">
                        @foreach($role->permissions->take(6) as $perm)
                        <span class="badge badge-gray px-2 py-0.5 text-[9px] uppercase font-bold">{{ str_replace(' ', '_', $perm->name) }}</span>
                        @endforeach
                        @if($role->permissions->count() > 6)
                        <span class="text-[10px] font-bold text-gray-400 self-center">+{{ $role->permissions->count() - 6 }} more</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="p-4 bg-gray-50 dark:bg-gray-800/80 flex items-center justify-center gap-3">
                <a href="{{ route('admin.roles.edit', $role) }}" class="btn-secondary btn-sm w-full font-bold">CONFIG ROLE</a>
                @if($role->name !== 'super-admin')
                <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="shrink-0" onsubmit="return confirm('Deleting role might lock out users assigned to this role. Continue?');">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-icon text-red-500 hover:text-red-700 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                </form>
                @endif
            </div>
        </div>
        @empty
        <div class="lg:col-span-3 text-center py-20 text-gray-400">No security roles defined.</div>
        @endforelse
    </div>
</div>
@endsection
